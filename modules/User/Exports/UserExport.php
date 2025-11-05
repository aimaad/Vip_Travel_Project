<?php
namespace Modules\User\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\User;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection()
    {
        return User::select([
            
            'first_name',
            'last_name',
            'email',
            'phone',
            'address',
            'city',
        ])->get();
    }

    public function map($user): array
    {
        return [
           
            ltrim($user->first_name,"=-"),
            ltrim($user->last_name,"=-"),
            ltrim($user->email,"=-"),
            ltrim($user->phone,"=-"),
            ltrim($user->address,"=-"),
            ltrim($user->city,"=-"),

        ];
    }

    public function headings(): array
    {
        return [
            __('First name'),
            __('Last name'),
            __('Email'),
            __('Phone'),
            __('Address'),
            __('City'),
        ];
    }
}
