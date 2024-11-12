<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowedItemResource\Pages;
use App\Filament\Resources\BorrowedItemResource\RelationManagers;
use App\Models\BorrowedItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BorrowedItemResource extends Resource
{
    protected static ?string $model = BorrowedItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Penyewa')
                    ->nullable()
                    ->default(fn () => auth()->user()->hasRole('siswa') ? auth()->id() : null)
                    ->hidden(fn () => auth()->user()->hasRole('siswa')), // Disable if role is 'siswa'
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->disabled(fn () => auth()->user()->hasRole('super_admin')), // Disable if role is 'siswa'
                Forms\Components\TextInput::make('description')
                    ->label('Deskripsi')
                    ->required(),
                Forms\Components\DatePicker::make('borrowed_at')
                    ->label('Tanggal Peminjaman')
                    ->required(),
                Forms\Components\DatePicker::make('returned_at')
                    ->label('Tanggal Pengembalian')
                    ->required(),
                Forms\Components\Repeater::make('borrowed_item_details')
                    ->relationship('borrowedItemDetails')
                    ->label('Daftar Barang')
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->relationship('item', 'note', function ($query) {
                                $query->where('status', 'Available')
                                      ->orderBy('name');
                            })
                            ->label('item')
                            ->nullable()
                            ->required(),
                        Forms\Components\Select::make('borrowed_condition')
                            ->label('Kondisi Awal')
                            ->options([
                                'Good' => 'Baik',
                                'Bad' => 'Buruk',
                            ]),
                        // Kondisi untuk menyembunyikan returned_condition saat create
                        Forms\Components\Select::make('returned_condition')
                            ->label('Kondisi Akhir')
                            ->options([
                                'Good' => 'Baik',
                                'Bad' => 'Buruk',
                            ])
                            ->visibleOn('edit'), // hanya muncul di halaman edit
                    ])
                    ->minItems(1)
                    ->columns(2),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->default('Pending') // Status otomatis 'Pending' saat create
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ])
                    ->disabledOn('create'), // Status tidak bisa diubah saat create
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Penyewa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('borrowed_at')
                    ->label('Tanggal Peminjaman')
                    ->sortable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('returned_at')
                    ->label('Tanggal Pengembalian')
                    ->sortable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make(name: 'booking_done')
                        ->label('Return Items')
                        ->action(function (BorrowedItem $record) {
                            foreach ($record->borrowedItemDetails as $detail) {
                                $detail->item->update(['status' => 'Available']);
                            }
                            $record->status = 'On Review'; // Set the booking status to Completed
                            $record->save();
                        })
                        ->visible(fn (BorrowedItem $record) =>
                            ($record->status === 'Approved')
                        ),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->action(function (BorrowedItem $record) {
                        $record->status = 'Approved';
                        $record->save();
                    })
                    ->visible(fn (BorrowedItem $record) => $record->status === 'Pending'),
                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->action(function (BorrowedItem $record) {
                            $record->status = 'Rejected';
                            $record->save();
                        })
                        ->visible(fn (BorrowedItem $record) => $record->status === 'Pending'),

                    ])->visible(fn () => auth()->user()->hasRole('super_admin')),

                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('approve_review')
                            ->label('Review Approved')
                            ->action(function (BorrowedItem $record) {
                                $record->status = 'Completed';
                                $record->save();
                            })
                            ->visible(fn (BorrowedItem $record) => $record->status === 'On Review'),
                        Tables\Actions\Action::make('rusak_atau_hilang')
                            ->label('Items Broken')
                            ->action(function (BorrowedItem $record) {
                                $record->status = 'Completed';
                                $record->save();
                            })
                            ->visible(fn (BorrowedItem $record) => $record->status === 'On Review'),
                    ])->visible(fn () => auth()->user()->hasRole('super_admin')),
                Tables\Actions\DeleteAction::make()
                ->visible(fn (BorrowedItem $record) => $record->status === 'Completed' || $record->status === 'Rejected'),
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrowedItems::route('/'),
            'create' => Pages\CreateBorrowedItem::route('/create'),
            'edit' => Pages\EditBorrowedItem::route('/{record}/edit'),
        ];
    }
}
