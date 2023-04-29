<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientInterface;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    protected ClientInterface $clientRepository;

    /**
     * Create a new controller instance.
     *
     * @param ClientInterface $clientRepository
     */
    public function __construct(ClientInterface $clientRepository)
    {
        $this->middleware('auth');
        $this->clientRepository = $clientRepository;
    }

    public function index()
    {
        $clients = $this->clientRepository->index();

        return view('clients.index', ['clients' => $clients]);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function importCsv(Request $request)
    {
        $clients = $this->clientRepository->importCsv($request);

        return redirect()->route('client.index')->with( ['clients' => $clients] );

    }

    public function clientFilter(Request $request)
    {
        $clients = $this->clientRepository->filterData($request)->orderBy('birthDate','DESC');
        $clients = $clients->paginate(20);

        return view('clients.index', ['clients' => $clients]);
    }

    public function exportCSV(Request $request)
    {
        $clients = $this->clientRepository->filterData($request)->get();
        $filename = "download_export.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Category', 'Firstname', 'Lastname','Email', 'Gender','BirthDate'));
        foreach($clients as $client) {
            fputcsv($handle, array($client->category, $client->firstname, $client->lastname, $client->email,$client->gender,$client->birthDate));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()->download(public_path($filename), $filename, $headers);
    }
}
