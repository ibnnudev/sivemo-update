<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cluster;
use App\Models\User;
use App\Repositories\Interface\AbjInterface;
use App\Repositories\Interface\ClusteringInterface;
use App\Repositories\Interface\LarvaeInterface;
use App\Repositories\Interface\RegencyInterface;
use App\Repositories\Interface\SampleInterface;
use Illuminate\Http\Request;

class ClusteringController extends Controller
{
    private $sample;

    private $larva;

    private $larvae;

    private $abj;

    private $regency;

    private $operation;

    public function __construct(
        SampleInterface $sample,
        LarvaeInterface $larva,
        AbjInterface $abj,
        LarvaeInterface $larvae,
        RegencyInterface $regency,
        ClusteringInterface $operation,
    ) {
        $this->sample  = $sample;
        $this->larva   = $larva;
        $this->abj     = $abj;
        $this->larvae  = $larvae;
        $this->regency = $regency;
        $this->operation = $operation;
    }

    public function index(Request $request)
    {
        // dd($this->larva->getAllForDashboard());
        return view('user.cluster', [
            'samplePerYear' => $this->sample->getSamplePerYear(date('Y')),
            'usersCount'    => User::all()->count(),
            'totalSample'   => $this->sample->getTotalSample(),
            'totalMosquito' => $this->sample->getTotalMosquito(),
            'totalLarva'    => $this->larva->getTotalLarva(),
            'abj'           => $this->abj->getAllGroupByDistrict(),
            'larvae'        => $this->larvae->getAll(),
            'sample'        => $this->sample->getAll(),
            'sampleAndAbj'  => $this->sample->getSampleAndAbjGroupByDistrict($request->regency_id ?? 3578),
            'regencies'     => $this->regency->getAll(),
        ]);
    }

    public function getSampleAndAbjByDistrict(Request $request)
    {
        return response()->json($this->sample->getSampleAndAbjGroupByDistrict($request->regency_id));
    }

    public function filterChartSamplePerYear(Request $request)
    {
        return response()->json($this->sample->getSamplePerYear($request->year));
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
        return view('admin.cluster.clustering', [
            'abj' => $this->abj->getAllGroupByDistrict()
        ]);
    }

    // Controller method
    public function filter(Request $request)
    {
        if ($request->wantsJson()) {
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
