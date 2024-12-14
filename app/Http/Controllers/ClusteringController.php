<?php

namespace App\Http\Controllers;

use App\Imports\ClusterSampleImport;
use App\Models\Cluster;
use App\Repositories\Interface\ClusteringInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ClusteringController extends Controller
{
    private $operation;

    public function __construct(ClusteringInterface $operation)
    {
        $this->operation = $operation;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Cluster::all();

            return datatables()->of($data)
                ->addColumn('code', function ($data) {
                    return $data->sample_code;
                })
                ->addColumn('province', function ($data) {
                    return $data->province;
                })
                ->addColumn('regency', function ($data) {
                    return $data->regency;
                })
                ->addColumn('district', function ($data) {
                    return $data->district;
                })
                ->addColumn('village', function ($data) {
                    return $data->village;
                })
                ->addColumn('latitude', function ($data) {
                    return $data->latitude;
                })
                ->addColumn('longitude', function ($data) {
                    return $data->longitude;
                })
                ->addColumn('morphotype_1', function ($data) {
                    return $data->morphotype_1 ?? 0;
                })
                ->addColumn('morphotype_2', function ($data) {
                    return $data->morphotype_2 ?? 0;
                })
                ->addColumn('morphotype_3', function ($data) {
                    return $data->morphotype_3 ?? 0;
                })
                ->addColumn('morphotype_4', function ($data) {
                    return $data->morphotype_4 ?? 0;
                })
                ->addColumn('morphotype_5', function ($data) {
                    return $data->morphotype_5 ?? 0;
                })
                ->addColumn('morphotype_6', function ($data) {
                    return $data->morphotype_6 ?? 0;
                })
                ->addColumn('morphotype_7', function ($data) {
                    return $data->morphotype_7 ?? 0;
                })
                ->addColumn('denv_1', function ($data) {
                    return $data->denv_1 ?? 0;
                })
                ->addColumn('denv_2', function ($data) {
                    return $data->denv_2 ?? 0;
                })
                ->addColumn('denv_3', function ($data) {
                    return $data->denv_3 ?? 0;
                })
                ->addColumn('denv_4', function ($data) {
                    return $data->denv_4 ?? 0;
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.cluster.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xls,xlsx',
        ]);

        try {
            $import = new ClusterSampleImport();
            Excel::import($import, $request->file('import_file'));

            return redirect()->back()->with('success', 'Data berhasil diimport');
        } catch (ValidationException $th) {
            $failures = $th->failures();

            return view('admin.cluster.index', compact('failures'));
        }
    }

    public function distance()
    {
        $samples = Cluster::all();

        $distances = $this->operation->calculateDistance($samples);

        return view('admin.cluster.distance', [
            'distances' => $distances,
        ]);
    }

    public function clustering(Request $request)
    {
        return view('admin.cluster.clustering');
    }

    // Controller method
    public function filter(Request $request)
    {
        if ($request->ajax()) {
            $epsilon = $request['epsilon'];
            $minPts = $request['minPts'];
            $offset = $request['offset'] ?? 0;
            $limit = 500; // Process 1000 items per request

            $dataset = Cluster::skip($offset)->take($limit)->get();
            $datasetArray = $dataset->map(function ($item) {
                return [
                    $item->latitude,
                    $item->longitude,
                ];
            })->toArray();

            $partialCluster = $this->operation->processCluster($datasetArray, $epsilon, $minPts);

            // Append each cluster with information from dataset
            $processedCluster = [];
            foreach ($partialCluster as $key => $value) {
                $processedCluster[$key] = collect($value)->map(function ($item) use ($dataset, $key) {
                    $point = $dataset->where('latitude', $item[0])->where('longitude', $item[1])->first();
                    if ($point) {
                        $point = $point->toArray();
                        $point['cluster'] = $key;
                    }
                    return $point;
                })->filter()->values()->toArray();
            }

            $totalCount = Cluster::count();
            $isComplete = ($offset + $limit) >= $totalCount;

            return response()->json([
                'cluster' => $processedCluster,
                'offset' => $offset + $limit,
                'isComplete' => $isComplete,
            ]);
        }
    }

    // Add a new method to get the initial data
    public function getInitialData()
    {
        $totalCount = Cluster::count();
        return response()->json([
            'totalCount' => $totalCount,
        ]);
    }
}
