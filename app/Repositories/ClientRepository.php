<?php


namespace App\Repositories;


use App\Interfaces\ClientInterface;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientRepository implements ClientInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * ClientRepository constructor.
     *
     * @param Client $client
     */

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        return $this->client::paginate(20);
    }

    public function importCsv(Request $request)
    {
        $file = $request->file('uploaded_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes

            $this->checkUploadedFileProperties($extension, $fileSize);

            $location = 'uploads';

            $file->move($location, $filename);

            $filepath = public_path($location . "/" . $filename);

            $file = fopen($filepath, "r");
            $importDataArray = array();

            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);

                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $importDataArray[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);
            $j = 0;

            foreach ($importDataArray as $importData) {
                $category = $importData[0] ?? '';
                $firstname = $importData[1] ?? '';
                $lastname = $importData[2] ?? '';
                $email = $importData[3] ?? '';
                $gender = $importData[4] ?? '';
                $birthDate = $importData[5] ?? '';
                $j++;

                try {
                    DB::beginTransaction();
                    $this->client::create([
                        'category' => $category,
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'email' => $email,
                        'gender' => $gender,
                        'birthDate' => $birthDate,
                    ]);
                    DB::commit();
                } catch (\Exception $e) {
                    //throw $e;
                    DB::rollBack();
                }
            }

            return $this->client::paginate(20);

        } else {

            return throw new \Exception('No file was uploaded', 500);
        }
    }

    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 2097152; // Uploaded file size limit is 2mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            } else {
                throw new \Exception('No file was uploaded', 413); //413 error
            }
        } else {
            throw new \Exception('Invalid file extension', 415); //415 error
        }
    }

    public function filterData($request)
    {
        $filters = collect([]);
        $filters->push($request->only(['filter_by_category', 'filter_by_gender', 'filter_by_birthDate', 'filter_by_age', 'filter_by_age_range']));
        return $this->client::when($request->filled('filter_by_category'), function ($query) use ($request) {
            return $query->where('category', $request->filter_by_category);
        })->when($request->filled('filter_by_gender'), function ($query) use ($request) {
            return $query->where('gender', $request->filter_by_gender);
        })->when($request->filled('filter_by_birthDate'), function ($query) use ($request) {
            return $query->where( 'birthDate', $request->filter_by_birthDate);
        })->when($request->filled('filter_by_age'), function ($query) use ($request) {
            $filteredDate = Carbon::now()->subYears($request->filter_by_age)->format('Y-m-d');
            return $query->where('birthDate', $filteredDate);
        })->when(($request->filled('first_age') && $request->filled('second_age') && ($request->second_age > $request->first_age)), function ($query) use ($request) {
            $filteredMinAge = Carbon::today()->subYears($request->second_age)->format('Y-m-d');
            $filteredMaxAge = Carbon::today()->subYears($request->fist_age)->format('Y-m-d');
            return $query->whereBetween('birthDate', [$filteredMinAge,$filteredMaxAge]);
        })->when($request->filled('filter_by_age'), function ($query) use ($request) {
            $filteredDate = Carbon::now()->subYears($request->filter_by_age)->format('Y-m-d');
            return $query->where('birthDate', $filteredDate);
        })->when($request->filled('first_age') && !$request->filled('second_age'), function ($query) use ($request) {
            $filteredMinAge = Carbon::now()->subYears($request->first_age)->format('Y-m-d');
            return $query->where('birthDate', '>=', $filteredMinAge);
        })->when($request->filled('second_age') && !$request->filled('first_age'), function ($query) use ($request) {
            $filteredMaxAge = Carbon::now()->subYears($request->second_age)->format('Y-m-d');
            return $query->where('birthDate', '<=', $filteredMaxAge);
        });
    }
}
