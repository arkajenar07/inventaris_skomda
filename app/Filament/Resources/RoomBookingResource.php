<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\RoomBookingResource\Pages;
    use App\Models\RoomBooking;
    use Filament\Forms;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Filters\SelectFilter;
    use Filament\Tables\Columns\ColumnGroup;
    use Filament\Tables\Columns\IconColumn;
    use View;

    class RoomBookingResource extends Resource
    {
        protected static ?string $model = RoomBooking::class;

        protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    // Pilihan Internal atau Eksternal
                    Forms\Components\Radio::make('booking_category')
                        ->label('Jenis Customer')
                        ->options([
                            'internal' => 'Internal',
                            'external' => 'External',
                        ])
                        ->reactive()
                        ->default('internal')
                        ->required()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            if ($get('booking_category') === 'internal') {
                                $set('total_price', 'FREE'); // Set to FREE for internal bookings
                            } else {
                                // Calculate total price for external bookings
                                $totalHarga = 0;
                                $tanggalPinjam = $get('borrowed_at');
                                $tanggalKembali = $get('returned_at');

                                if ($tanggalPinjam && $tanggalKembali) {
                                    $jumlahHari = \Carbon\Carbon::parse($tanggalPinjam)->diffInDays(\Carbon\Carbon::parse($tanggalKembali)) + 1;
                                    $jumlahHari = max(1, $jumlahHari); // Minimal 1 hari
                                } else {
                                    $jumlahHari = 1; // Jika salah satu tanggal belum diisi, default ke 1
                                }

                                $roomBookingDetails = $get('room_booking_details') ?? [];
                                foreach ($roomBookingDetails as $detail) {
                                    if (isset($detail['room_id'])) {
                                        $room = \App\Models\Room::find($detail['room_id']);
                                        $totalHarga += $room->price * $jumlahHari;
                                    }
                                }

                                $set('total_price', $totalHarga); // Set calculated price for external bookings
                            }
                        })
                        ->live()
                        ->visible(fn () => auth()->user()->hasRole('super_admin')),

                    // Form untuk Internal
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Penyewa')
                        ->nullable()
                        ->default(fn () => auth()->user()->hasRole('siswa') ? auth()->id() : null)
                        ->hidden((fn ($get) => $get('booking_category') === 'external') || (fn () => auth()->user()->hasRole('siswa'))),

                    Forms\Components\Hidden::make('user_id')
                        ->default(fn () => auth()->id())
                        ->disabled(fn () => auth()->user()->hasRole('super_admin')),
                    // Form Group untuk Eksternal
                    Forms\Components\Fieldset::make('External Customer Details')
                        ->label('Customer Eksternal')
                        ->relationship('roomBookingExternal')
                        ->hidden(fn ($get) => $get('booking_category') !== 'external')
                        ->schema([
                            Forms\Components\TextInput::make('customer_name')
                                ->label('Nama Customer Eksternal')
                                ->required(),
                            Forms\Components\FileUpload::make('customer_photo')
                                ->label('Foto Customer')
                                ->image()
                                ->required(),
                            Forms\Components\TextInput::make('customer_phone')
                                ->label('Nomor Telepon')
                                ->required(),
                            Forms\Components\TextInput::make('customer_company')
                                ->label('Perusahaan')
                                ->required(),
                            Forms\Components\Select::make('payment_status')
                                ->label('Status Pembayaran')
                                ->default('Pending')
                                ->options([
                                    'Pending' => 'Pending',
                                    'Done' => 'Done',
                                ])
                                ->disabledOn('create'),
                        ])
                        ->visible(fn () => auth()->user()->hasRole('super_admin')),

                    // Form umum untuk room booking
                    Forms\Components\TextInput::make('description')
                        ->label('Deskripsi')
                        ->required(),
                    Forms\Components\DatePicker::make('borrowed_at')
                        ->label('Tanggal Peminjaman')
                        ->required(),
                    Forms\Components\DatePicker::make('returned_at')
                        ->label('Tanggal Pengembalian')
                        ->required(),

                    // Detail ruangan dengan kondisi awal dan akhir
                    // Form umum untuk room booking
                    Forms\Components\Repeater::make('room_booking_details')
                    ->relationship('roomBookingDetails')
                    ->label('Daftar Ruangan')
                    ->schema([
                        Forms\Components\Select::make('room_id')
                            ->relationship('room', 'name', function ($query) {
                                $query->where('status', 'Available')
                                      ->orderByRaw("CASE WHEN name LIKE '%Ruang%' THEN 0 ELSE 1 END")
                                      ->orderByRaw("CAST(REGEXP_SUBSTR(name, '[0-9]+') AS UNSIGNED) ASC")
                                      ->orderBy('name');
                            })
                            ->label('Room')
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->description}"),
                        Forms\Components\Select::make('borrowed_condition')
                            ->label('Kondisi Awal')
                            ->options([
                                'Good' => 'Baik',
                                'Bad' => 'Buruk',
                            ]),
                        Forms\Components\Select::make('returned_condition')
                            ->label('Kondisi Akhir')
                            ->options([
                                'Good' => 'Baik',
                                'Bad' => 'Buruk',
                            ])
                            ->visibleOn('edit'),
                    ])
                    ->minItems(1)
                    ->columns(2)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        if ($get('booking_category') === 'internal') {
                            $set('total_price', 'FREE'); // Set to FREE for internal bookings
                        } else {
                            // Calculate total price for external bookings
                            $totalHarga = 0;
                            $tanggalPinjam = $get('borrowed_at');
                            $tanggalKembali = $get('returned_at');

                            if ($tanggalPinjam && $tanggalKembali) {
                                $jumlahHari = \Carbon\Carbon::parse($tanggalPinjam)->diffInDays(\Carbon\Carbon::parse($tanggalKembali)) + 1;
                                $jumlahHari = max(1, $jumlahHari); // Minimal 1 hari
                            } else {
                                $jumlahHari = 1; // Jika salah satu tanggal belum diisi, default ke 1
                            }

                            $roomBookingDetails = $get('room_booking_details') ?? [];
                            foreach ($roomBookingDetails as $detail) {
                                if (isset($detail['room_id'])) {
                                    $room = \App\Models\Room::find($detail['room_id']);
                                    $totalHarga += $room->price * $jumlahHari;
                                }
                            }

                            $set('total_price', $totalHarga); // Set calculated price for external bookings
                        }
                    })
                    ->live(),

                    Forms\Components\TextInput::make('total_price')
                        ->label('Total Harga')
                        ->prefix('Rp')
                        ->disabled() // Prevent user editing
                        ->default(fn ($get) => $get('booking_category') === 'internal' ? 'FREE' : 0) // Set initial value based on booking_category
                        ->reactive() // Ensure it updates when booking_category changes
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            if ($get('booking_category') === 'external') {
                                // Recalculate total_price if external
                                self::calculateTotalPrice($get, $set);
                            } else {
                                $set('total_price', 'FREE');
                            }
                        })
                        ->hiddenOn('edit'), // Hide in edit mode if needed


                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->default('Pending')
                        ->options([
                            'Pending' => 'Pending',
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                        ])
                        ->hiddenOn('create')
                        ->disabledOn('create'),
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    ColumnGroup::make('Customers infos', [
                        TextColumn::make('user.name')
                            ->label('Internal Users')
                            ->default('-'),
                        TextColumn::make('roomBookingExternal.customer_name')
                            ->label('External Customers')
                            ->default('-'),
                    ]),
                    TextColumn::make('transaction_date')->label('Transaction Date')->date(),
                    TextColumn::make('borrowed_at')->label('Borrowed At')->date(),
                    TextColumn::make('returned_at')->label('Returned At')->date(),
                    TextColumn::make('status')->label('Status'),

                    // Tambahkan kolom untuk menampilkan harga
                    TextColumn::make('total_price')
                        ->label('Price')
                ])
                ->filters([
                    SelectFilter::make('status')
                        ->options([
                            'Pending' => 'Pending',
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                        ]),
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                    // Tables\Actions\Action::make('invoice')
                    //     ->label('View Invoice')
                    //     ->action(fn (RoomBooking $record) => route('invoice.show', ['id' => $record->id]))
                    //     ->modalContent(fn (RoomBooking $record) => view('components.order-details', ['record' => $record]))
                    //     ->visible(fn (RoomBooking $record) => $record->roomBookingExternal &&
                    //                                           $record->roomBookingExternal->payment_status === 'Pending' &&
                    //                                           $record->status === 'Approved'),
                    Tables\Actions\Action::make('booking_done')
                        ->label('Booking Done')
                        ->action(function (RoomBooking $record) {
                            foreach ($record->roomBookingDetails as $detail) {
                                $detail->room->update(['status' => 'Available']);
                            }
                            $record->status = 'Completed'; // Set the booking status to Completed
                            $record->save();
                        })
                        ->visible(fn (RoomBooking $record) =>
                            ($record->booking_category === 'internal' && $record->status === 'Approved') ||
                            ($record->booking_category === 'external' &&
                             $record->status === 'Approved' &&
                             $record->roomBookingExternal &&
                             $record->roomBookingExternal->payment_status === 'Done')
                        ),
                    Tables\Actions\Action::make('payment_done')
                        ->label('Payment Done')
                        ->action(function (RoomBooking $record) {
                            if ($record->roomBookingExternal) {
                                $record->roomBookingExternal->update(['payment_status' => 'Done']);
                            }
                        })
                        ->visible(fn (RoomBooking $record) => $record->roomBookingExternal &&
                                                              $record->roomBookingExternal->payment_status === 'Pending' &&
                                                              $record->status === 'Approved'),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (RoomBooking $record) => $record->status === 'Completed' || $record->status === 'Rejected'),
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->action(function (RoomBooking $record) {
                            $record->status = 'Approved';
                            $record->save();
                        })
                        ->visible(fn (RoomBooking $record) => $record->status === 'Pending'),
                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->action(function (RoomBooking $record) {
                            foreach ($record->roomBookingDetails as $detail) {
                                $detail->room->update(['status' => 'Available']);
                            }
                            $record->status = 'Rejected';
                            $record->save();
                        })
                        ->visible(fn (RoomBooking $record) => $record->status === 'Pending'),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
        }


        public static function getRelations(): array
        {
            return [];
        }

        public static function getPages(): array
        {
            return [
                'index' => Pages\ListRoomBookings::route('/'),
                'create' => Pages\CreateRoomBooking::route('/create'),
                'edit' => Pages\EditRoomBooking::route('/{record}/edit'),
            ];
        }
    }
