<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
class ContactsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;
    public $group_id = '';

    public function __construct($group_id)
    {
        $this->group_id = $group_id;
    }
    public function rules(): array
    {
        return [
            'number' => 'required',
        ];
    }

    /**
     * @param Collection $rows
     * @throws \Throwable
     */
    public function collection(Collection $rows)
    {

        $errorMsg = "";
        DB::beginTransaction();

        $i = 1;
        $user = auth('customer')->user();
        foreach ($rows as $key => $row) {

            if ($row['number']) {

                //You can validate other values using same steps.

                $data['number'] = "+".str_replace('+','',$row['number']);
                $data['first_name'] = $row['first_name'];
                $data['last_name'] = $row['last_name'];
                $data['email'] = $row['email'];
                $data['company'] = $row['company'];
                $contact = auth('customer')->user()->contacts()->create($data);

                $contactGroup = new ContactGroup();
                $contactGroup->customer_id = $user->id;
                $contactGroup->group_id = $this->group_id;
                $contactGroup->contact_id = $contact->id;
                $contactGroup->save();


                if (!$contact || !$contactGroup) {
                    $errorMsg = "Error while inserting";
                    break;
                }
                $i++;
            }
        }


        if (!empty($errorMsg)) {
            // Rollback in case there is error
            DB::rollBack();

            //  return redirect()->back()->withErrors(['error' => $errorMsg]);
        } else {
            // Commit to database
            DB::commit();


            //   return redirect()->back()->withErrors(['success' => 'Uploaded Successfully']);
        }
    }

}
