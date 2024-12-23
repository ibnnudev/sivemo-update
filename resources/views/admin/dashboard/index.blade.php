<x-app-layout>
    <x-breadcrumb name="dashboard" />
    <div class="space-y-6">
        <div class="z-0 relative mb-4" style="height: 350px; border-radius: 6px;">
            <!-- Legenda -->
            <div class="absolute bottom-0 right-0 p-2 mr-2 mb-2 bg-white shadow text-xs" style="z-index: 2;">
                <h5 class="mb-2 legend-text text-xs ">Legend</h5>
                <ul class="list-unstyled">
                    <li>
                        <span class="legend-color legend-green text-xs"></span>
                        ABJ Normal
                    </li>
                    <li>
                        <span class="legend-color legend-red text-xs"></span>
                        ABJ Tidak Normal
                    </li>
                    <!-- Tambahkan elemen li sesuai dengan legenda Anda -->
                </ul>
            </div>
            <!-- Peta -->
            <div id="map" style="height: 100%; position: relative; z-index: 1;"></div>
        </div>
        <div class="grid grid-cols-4 gap-4">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow flex items-center mb-4 md:mb-0">
                <i class="fas fa-users fa-2x text-primary mr-4 "></i>
                <div>
                    <a href="#">
                        <h5 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                            {{ number_format($usersCount, 0, ',', '.') }}
                        </h5>
                    </a>
                    <p class="font-normal text-sm text-gray-500">Pengguna</p>
                </div>
            </div>
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow flex items-center mb-4 md:mb-0">
                <i class="fas fa-chart-simple fa-2x text-success mr-4"></i>
                <div>
                    <a href="#">
                        <h5 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                            {{ number_format($totalSample, 0, ',', '.') }}
                        </h5>
                    </a>
                    <p class="font-normal text-sm text-gray-500">Sampel Nyamuk</p>
                </div>
            </div>
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow flex items-center mb-4 md:mb-0">
                <i class="fas fa-mosquito fa-2x text-error mr-4"></i>
                <div>
                    <a href="#">
                        <h5 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                            {{ number_format($totalMosquito, 0, ',', '.') }}
                        </h5>
                    </a>
                    <p class="font-normal text-sm text-gray-500">Total Nyamuk</p>
                </div>
            </div>
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow flex items-center mb-4 md:mb-0">
                <i class="fas fa-worm fa-2x text-warning mr-4"></i>
                <div>
                    <a href="#">
                        <h5 class="mb-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                            {{ number_format($totalLarva, 0, ',', '.') }}
                        </h5>
                    </a>
                    <p class="font-normal text-sm text-gray-500">Total Larva</p>
                </div>
            </div>
        </div>

        <x-card-container class="mb-6 text-sm">
            <h2 class="font-semibold mb-1">Sesuaikan Klaster</h2>
            <p class="mb-8 text-gray-500">Hasil klaster terlihat pada peta diatas. Anda dapat menyesuaikan klaster
                dengan mengubah
                nilai epsilon
                dan min points.</p>
            <div class="flex items-end gap-4">
                <x-input id="epsilon" label="Epsilon" name="epsilon" type="number" value="0.002839" required />
                <x-input id="minPoints" label="Min Points" name="minPoints" type="number" value="1" required />
                <x-button type="submit" class="bg-primary mb-4" id="buttonDbscan">Klasterkan</x-button>
            </div>
        </x-card-container>

        {{-- <x-card-container class="mb-4 hidden" id="jumlahKlasterContainer">
            <h2 class="font-semibold text-xs mb-8">Jumlah Klaster Terbentuk</h2>
            <div class="clusterContainer grid grid-cols-4 gap-6"></div>
        </x-card-container> --}}

        <x-card-container>
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-semibold">Statistik Sampel</p>
                    <small class="text-gray-400">Jumlah sampel nyamuk yang diperiksa</small>
                </div>
                <x-select id="filterSamplePerYearChart" name="filterSamplePerYearChart">
                    @php
                        $years = [];
                        for ($i = 2021; $i <= date('Y'); $i++) {
                            $years[] = $i;
                        }
                    @endphp

                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </x-select>
            </div>
            <canvas id="samplePerYear"></canvas>
        </x-card-container>
        <x-card-container class="mt-8" id="sampleAbjCard" style="height: 410px; max-height: 100%; overflow: hidden">
            <div class="md:flex justify-between items-center">
                <p class="text-sm font-semibold">
                    Data Sampel dan ABJ (%)
                </p>
                <x-select id="regency" name="regency" label="Kabupaten">
                    @foreach ($regencies as $regency)
                        <option value="{{ $regency->id }}">{{ $regency->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <canvas id="sampleAndAbj"></canvas>
        </x-card-container>
    </div>

    @push('js-internal')
        <script src="https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.js"></script>
        <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
        <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>

        <script>
            function getColor(abj_total) {
                if (abj_total <= 95) {
                    return '#e74a3b'; // ABJ Sedang
                } else {
                    return '#1cc88a'; // ABJ Rendah1cc88a
                }

            }

            const map = L.map('map').setView([-7.2756196, 112.7106256], 8);

            var markers = L.markerClusterGroup();

            const MAPBOX_ACCESS_TOKEN = "{{ config('app.mapbox_token') }}";

            L.tileLayer(
                'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: '&copy; <a href="https://www.mapbox.com/">Mapbox</a> &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
                    maxZoom: 18,
                    id: 'mapbox/light-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: MAPBOX_ACCESS_TOKEN,
                    attribution: '',
                    detectRetina: true,
                }


            ).addTo(map);

            @if (count($abj) > 0)
                function updateMapData() {
                    // Menggunakan fetch untuk mengambil data GeoJSON dari URL
                    let abj = Object.values(@json($abj));
                    fetch("{{ asset('assets/geojson/surabaya.json') }}")
                        .then((response) => response.json())
                        .then((data) => {
                            const geojson = {
                                type: 'FeatureCollection',
                                features: []
                            };

                            data.features.forEach((feature) => {
                                const properties = feature.properties;
                                const kecamatan = properties.KECAMATAN;

                                abj.forEach((abjItem) => {
                                    if (abjItem.district === kecamatan) {
                                        // Sekarang Anda memiliki array koordinat dari fitur yang sesuai
                                        const coordinates = feature.geometry.coordinates;

                                        // Ubah koordinat jika diperlukan
                                        const coordinates2 = coordinates[0];
                                        // console.log(coordinates2);

                                        geojson.features.push({
                                            type: 'Feature',
                                            geometry: {
                                                type: 'Polygon',
                                                coordinates: [coordinates2]
                                            },
                                            properties: {
                                                color: getColor(abjItem.abj_total),
                                                regency: abjItem.regency,
                                                district: properties.KECAMATAN,
                                                village: properties.KELURAHAN,
                                                abj: abjItem.abj_total,
                                                total_sample: abjItem.total_sample,
                                                total_check: abjItem.total_check
                                            }
                                        });
                                    }
                                });
                            });

                            L.geoJSON(geojson, {
                                style: function(feature) {
                                    return {
                                        fillColor: feature.properties.color,
                                        color: feature.properties.color,
                                        weight: 0.5,
                                        fillOpacity: 0.5,
                                    };
                                },
                                onEachFeature: function(feature, layer) {
                                    layer.on('click', function(e) {
                                        const coordinates = e.latlng;
                                        const properties = feature.properties;

                                        const popupContent = `
                                        <p><strong>Kabupaten/Kota:</strong> ${properties.regency}</p>
                                        <p><strong>Kecamatan:</strong> ${properties.district}</p>
                                        <p><strong>ABJ:</strong> ${properties.abj}%</p>
                                        <p><strong>Total Sampel:</strong> ${properties.total_sample}</p>
                                        <p><strong>Total Pemeriksaan:</strong> ${properties.total_check}</p>
                                    `;

                                        L.popup()
                                            .setLatLng(coordinates)
                                            .setContent(popupContent)
                                            .openOn(map);

                                        // Zoom to the clicked feature
                                        map.fitBounds(layer.getBounds(), {
                                            padding: [100, 100]
                                        });
                                    });

                                    layer.on('mouseover', function(e) {
                                        map.getContainer().style.cursor = 'pointer';
                                    });

                                    layer.on('mouseout', function(e) {
                                        map.getContainer().style.cursor = '';
                                    });
                                }
                            }).addTo(map);
                        })
                        .catch((error) => {
                            console.error("Gagal mengambil data GeoJSON:", error);
                        });


                }

                updateMapData(); // map update
            @endif

            let larvae = @json($larvae);
            let sample = Object.values(@json($sample));

            @if ($sample->count() > 0)
                let centerCoordinateSample = [];
                for (let i = 0; i < sample.length; i++) {
                    centerCoordinateSample.push([sample[i].latitude, sample[i].longitude, sample[i]]);
                }

                centerCoordinateSample.forEach(coordinate => {
                    var el = L.divIcon({
                        className: 'custom-marker',
                        html: '<img src="{{ asset('assets/images/vector/mosquito-icon.png') }}" class="w-6 h-6">'
                    });

                    // cluster marker
                    markers.addLayer(L.marker([parseFloat(coordinate[0]), parseFloat(coordinate[1])], {
                        icon: el
                    }).bindPopup(`
                        <table class="border-collapse border-none">
                            <tbody>
                                <tr>
                                    <th colspan="3" class="p-0">Detail Lokasi</th>
                                </tr>
                                <tr>
                                    <td class="p-0 font-semibold">Provinsi</td>
                                    <td class="p-0">:</td>
                                    <td class="p-0">${coordinate[2].province.name}</td>
                                </tr>
                                <tr>
                                    <td class="p-0 font-semibold">Kabupaten</td>
                                    <td class="p-0">:</td>
                                    <td class="p-0">${coordinate[2].regency.name}</td>
                                </tr>
                                <tr>
                                    <td class="p-0 font-semibold">Kecamatan</td>
                                    <td class="p-0">:</td>
                                    <td class="p-0">${coordinate[2].district.name}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Lokasi</td>
                                    <td>: </td>
                                    <td>${coordinate[2].location_name}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Rumah Sakit</td>
                                    <td>: </td>
                                    <td>${coordinate[2].public_health_name ?? '-'}</td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="border-collapse border-none mt-4 w-full">
                            <thead>
                                <tr>
                                    <th colspan="2" class="p-0">Detail Sampling</th>
                                </tr>
                                <tr class="mt-3">
                                    <th colspan="2" class="p-0">Jenis Virus</th>
                                    <th class="p-0">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                ` +
                        Object.values(coordinate[2].type).map(function(type) {
                            return `
                                <tr>
                                    <td class="p-0 font-medium">${type.name}:</td>
                                    <td class="p-0 text-right">${type.amount}</td>
                                </tr>
                            `;
                        }).join('') +
                        `
                            </tbody>
                        </table>
                    `).on('mouseover', function() {
                        this.openPopup();
                    }).on('mouseout', function() {
                        this.closePopup();
                    }));

                    map.addLayer(markers);
                });
            @endif

            @if ($larvae->count() > 0)
                for (let i = 0; i < larvae.length; i++) {
                    let detailContent = '';

                    // Check if detail_larvaes is empty
                    if (larvae[i].detail_larvaes.length === 0) {
                        detailContent = '<p class="text-center">Tidak ada informasi detail / tidak lengkap</p>';
                    } else {
                        detailContent = `<ul class="list-none list-inside">`;
                        larvae[i].detail_larvaes.map((detail) => {
                            console.log(detail);
                            detailContent += `<li><strong>Jenis TPA:</strong> ${detail.tpa_type.name}</li>`;
                            detailContent += `<li><strong>Jml. Larva:</strong> ${detail.amount_larva}</li>`;
                            detailContent += `<li><strong>Jml. Telur:</strong> ${detail.amount_egg}</li>`;
                            detailContent += `<li><strong>Larva Dewasa:</strong> ${detail.number_of_adults}</li>`;
                            detailContent += `<li><strong>Temp. Air:</strong> ${detail.water_temperature} °C</li>`;
                            detailContent += `<li><strong>PH Air:</strong> ${detail.ph}</li>`;
                            detailContent += `<li><strong>Salinitas:</strong> ${detail.salinity}</li>`;
                            detailContent +=
                                `<li><strong>Tumbuhan:</strong> ${detail.aquatic_plant == 'available' ? 'Ada' : 'Tidak Ada'} </li>`;
                        });
                        detailContent += `</ul>`;
                    }

                    let marker = L.marker([larvae[i].latitude, larvae[i].longitude], {
                        icon: L.divIcon({
                            html: `<img src="{{ asset('assets/images/larvae/icon.jpg') }}" class="w-6 h-6">`,
                            className: 'text-white bg-transparent',
                            iconAnchor: [12, 12], // Adjust these values to correctly position your icon
                            popupAnchor: [0, -20] // Adjust the popup to appear above the icon
                        })
                    });

                    marker.bindPopup(detailContent).on('popupopen', function() {
                        $('.leaflet-popup-content').width('w-96');
                    });

                    // Show popup on hover
                    marker.on('mouseover', function() {
                        marker.openPopup();
                    });

                    // Zoom to marker when clicked
                    marker.on('click', function() {
                        map.setView([larvae[i].latitude, larvae[i].longitude], 15);
                    });

                    marker.addTo(map);
                }

                // Add layer to map
                map.addLayer(markers);
            @endif

            let listDistrict = [];
            let allClusters = {};
            let offset = 0;
            let isProcessing = false;

            // Namespace to encapsulate global variables
            const clusteringData = {
                allClusters: {},
                offset: 0,
                isProcessing: false,
                listDistrict: []
            };

            function pollClusterData(epsilon, minPoints) {
                $.ajax({
                    url: "{{ route('admin.cluster.filter') }}",
                    type: "GET",
                    data: {
                        epsilon: epsilon,
                        minPts: minPoints,
                        offset: clusteringData.offset
                    },
                    success: function(response) {
                        processClusterChunk(response.cluster);

                        if (response.isComplete) {
                            finalizeClustering();
                        } else {
                            clusteringData.offset = response.offset;
                            setTimeout(() => pollClusterData(epsilon, minPoints), 1000); // 1 second delay
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error processing chunk:", error);
                        clusteringData.isProcessing = false;
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Gagal memproses data. Coba lagi nanti."
                        });
                    }
                });
            }

            function processClusterChunk(clusterChunk) {
                for (let key in clusterChunk) {
                    if (!clusteringData.allClusters[key]) {
                        clusteringData.allClusters[key] = [];
                    }
                    clusteringData.allClusters[key] = clusteringData.allClusters[key].concat(clusterChunk[key]);
                }
            }

            function finalizeClustering() {
                clusteringData.isProcessing = false;
                setupClustering(Object.values(clusteringData.allClusters));

                // Update UI
                $("#buttonDbscan").text("Klasterkan").prop("disabled", false);
                Swal.fire({
                    icon: "success",
                    title: "Klasterisasi",
                    text: "Klasterisasi data berhasil dilakukan",
                });
            }

            async function setupClustering(data) {
                // $("#jumlahKlasterContainer").removeClass("hidden");

                let colorMap = {};
                const generateColor = () => `#${Math.floor(Math.random() * 16777215).toString(16)}`;

                data.forEach((clusterItems, index) => {
                    clusteringData.listDistrict.push(clusterItems.map(item => item));

                    let clusterCenter = clusterItems.reduce((acc, item) => {
                        acc.lat += parseFloat(item.latitude);
                        acc.lon += parseFloat(item.longitude);
                        return acc;
                    }, {
                        lat: 0,
                        lon: 0
                    });

                    clusterCenter.lat /= clusterItems.length;
                    clusterCenter.lon /= clusterItems.length;

                    clusterItems.forEach(item => {
                        const clusterColor = colorMap[item.cluster] || (colorMap[item.cluster] =
                            generateColor());

                        let markerIcon = L.divIcon({
                            className: "custom-div-icon",
                            html: `<div style="background-color: ${clusterColor}; width: 10px; height: 10px; border-radius: 50%;"></div>`
                        });

                        L.marker([item.latitude, item.longitude], {
                                icon: markerIcon
                            })
                            .addTo(map)
                            .bindPopup(generatePopupContent(item));
                    });

                    // Add circle for each cluster center
                    const clusterColor = colorMap[index];
                    L.circle([clusterCenter.lat, clusterCenter.lon], {
                            radius: 400,
                            color: clusterColor,
                            fill: true,
                            fillOpacity: 0.1,
                            weight: 2
                        })
                        .addTo(map)
                        .on("click", function() {
                            // Calculate data for popup
                            let clusterCount = clusterItems.length;
                            let totalDENV = 0;
                            let totalDENV1 = 0;
                            let totalDENV2 = 0;
                            let totalDENV3 = 0;
                            let totalDENV4 = 0;
                            let totalMorphotype = 0;
                            let listOfDistric = [];

                            clusterItems.forEach((i) => {
                                listOfDistric.push(i.district ?? "-");
                                totalDENV += i.denv_1 + i.denv_2 + i.denv_3 + i.denv_4;
                                totalDENV1 += i.denv_1;
                                totalDENV2 += i.denv_2;
                                totalDENV3 += i.denv_3;
                                totalDENV4 += i.denv_4;
                                totalMorphotype +=
                                    i.morphotype_1 +
                                    i.morphotype_2 +
                                    i.morphotype_3 +
                                    i.morphotype_4 +
                                    i.morphotype_5 +
                                    i.morphotype_6 +
                                    i.morphotype_7;
                            });

                            // Generate popup content
                            let table = `<table class="table-auto">
                <tbody>
                    <tr>
                        <td class="border font-semibold px-2">Nomor Cluster</td>
                        <td class="border">${index + 1}</td>
                    </tr>
                    <tr>
                        <td class="border font-semibold px-2">Jumlah Data</td>
                        <td class="border">${clusterCount}</td>
                    </tr>
                    <tr>
                        <td class="border font-semibold px-2">Jumlah DENV 1</td>
                        <td class="border">${totalDENV1 ?? "-"}</td>
                    </tr>
                    <tr>
                        <td class="border font-semibold px-2">Jumlah DENV 2</td>
                        <td class="border">${totalDENV2 ?? "-"}</td>
                    </tr>
                    <tr>
                        <td class="border font-semibold px-2">Jumlah DENV 3</td>
                        <td class="border">${totalDENV3 ?? "-"}</td>
                    </tr>
                    <tr>
                        <td class="border font-semibold px-2">Jumlah DENV 4</td>
                        <td class="border">${totalDENV4 ?? "-"}</td>
                    </tr>
                    <tr>
                        <td class="border font-semibold px-2">Jumlah DENV</td>
                        <td class="border">${totalDENV ?? "-"}</td>
                    </tr>
                    <tr>
                        <td class="border font-semibold px-2">Jumlah Morfotipe</td>
                        <td class="border">${totalMorphotype ?? "-"}</td>
                    </tr>
                    <tr>
                        <td class="border font-semibold px-2">Lokasi</td>
                        <td class="border">${
                            [...new Set(listOfDistric)].join(", ") ?? "-"
                        }</td>
                    </tr>
                </tbody>
            </table>`;

                            L.popup()
                                .setLatLng([clusterCenter.lat, clusterCenter.lon])
                                .setContent(table)
                                .openOn(map);
                        });
                });
            }


            function generatePopupContent(item) {
                return `
                <table class="table-auto">
                    <tbody>
                        <tr><td class="font-semibold px-2">Lokasi</td><td>${item.location_name}</td></tr>
                        <tr><td class="font-semibold px-2">Jenis Lokasi</td><td>${item.location_type}</td></tr>
                        <tr><td class="font-semibold px-2">Koordinat</td><td>${item.latitude} | ${item.longitude}</td></tr>
                        <tr><td class="font-semibold px-2">Morfotipe</td><td>${generateMorphotypeList(item)}</td></tr>
                        <tr><td class="font-semibold px-2">DENV</td><td>${generateDenvList(item)}</td></tr>
                    </tbody>
                </table>`;
            }

            function generateMorphotypeList(item) {
                return [
                    `Morf. 1: ${item.morphotype_1}`,
                    `Morf. 2: ${item.morphotype_2}`,
                    `Morf. 3: ${item.morphotype_3}`,
                    `Morf. 4: ${item.morphotype_4}`,
                    `Morf. 5: ${item.morphotype_5}`,
                    `Morf. 6: ${item.morphotype_6}`,
                    `Morf. 7: ${item.morphotype_7}`
                ].join('<br>');
            }

            function generateDenvList(item) {
                return [
                    `DENV. 1: ${item.denv_1 ?? "-"}`,
                    `DENV. 2: ${item.denv_2 ?? "-"}`,
                    `DENV. 3: ${item.denv_3 ?? "-"}`,
                    `DENV. 4: ${item.denv_4 ?? "-"}`
                ].join('<br>');
            }

            $("#buttonDbscan").on("click", function() {
                // Reset UI
                $("#klasterContainer").addClass("hidden");
                // $("#jumlahKlasterContainer").addClass("hidden");
                $("#buttonDbscan").text("Sedang memproses data...").prop("disabled", true);

                Swal.fire({
                    title: "Sedang memproses data...",
                    html: "Mohon tunggu sebentar",
                    timerProgressBar: true,
                    didOpen: () => Swal.showLoading(),
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                const epsilon = parseFloat($("#epsilon").val()) || 0;
                const minPoints = parseInt($("#minPoints").val()) || 1;

                if (epsilon <= 0 || minPoints <= 0) {
                    Swal.fire({
                        icon: "error",
                        title: "Input Tidak Valid",
                        text: "Pastikan nilai epsilon dan minimum poin lebih besar dari 0."
                    });
                    return;
                }

                // Reset variables
                clusteringData.allClusters = {};
                clusteringData.offset = 0;
                clusteringData.isProcessing = true;
                clusteringData.listDistrict = [];
                $(".clusterContainer").empty();

                pollClusterData(epsilon, minPoints);
            });

            // full screen
            L.control.fullscreen().addTo(map);

            // automatically push buttonDbScan after page loaded
            $(document).ready(function() {
                $("#buttonDbscan").click();
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                let samplePerYear = @json($samplePerYear);
                let myChart;

                // Ensure the canvas element exists before attempting to access it
                let ctx = document.getElementById('samplePerYear');
                if (ctx) {
                    ctx = ctx.getContext('2d');
                    ctx.canvas.width = '100%';

                    let purplePallete = [
                        '#4e73df',
                        '#6f42c1',
                        '#9c27b0',
                    ];

                    function updateChart(data) {
                        var labels = data.map(entry => entry.month);
                        var mosquitoTypes = Object.keys(data[0].type);
                        var mosquitoAmounts = data.map(entry => Object.values(entry.type));

                        var datasets = mosquitoTypes.map((type, index) => {
                            return {
                                label: type,
                                data: mosquitoAmounts.map(amounts => amounts[index]),
                                backgroundColor: purplePallete[index],
                                borderColor: purplePallete[index],
                                borderWidth: 1,
                                fill: false,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                pointHitRadius: 10,
                                pointBackgroundColor: purplePallete[index],
                                pointBorderColor: purplePallete[index],
                                pointHoverBackgroundColor: purplePallete[index],
                                pointHoverBorderColor: purplePallete[index],
                            };
                        });

                        if (myChart) {
                            myChart.destroy();
                        }

                        myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                interaction: {
                                    mode: 'index',
                                    intersect: false
                                },
                                scales: {
                                    y: {
                                        stacked: true,
                                        grid: {
                                            display: false,
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            precision: 0,
                                            stepSize: 1,
                                        },
                                    },
                                    x: {
                                        stacked: true,
                                        grid: {
                                            display: false,
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            precision: 0,
                                            stepSize: 1,
                                        },
                                    },
                                },
                                plugins: {
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false
                                    },
                                    legend: {
                                        labels: {
                                            usePointStyle: true,
                                            boxWidth: 5,
                                            boxHeight: 5,
                                        },
                                    },
                                },
                            }
                        });
                    }

                    function emptyChart() {
                        if (myChart) {
                            myChart.destroy();
                        }

                        myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: [],
                                datasets: []
                            },
                            options: {
                                responsive: true,
                                interaction: {
                                    mode: 'index',
                                    intersect: false
                                },
                                scales: {
                                    y: {
                                        stacked: true,
                                        grid: {
                                            display: false,
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            precision: 0,
                                            stepSize: 1,
                                        },
                                    },
                                    x: {
                                        stacked: true,
                                        grid: {
                                            display: false,
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            precision: 0,
                                            stepSize: 1,
                                        },
                                    },
                                },
                                plugins: {
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false
                                    },
                                    legend: {
                                        labels: {
                                            usePointStyle: true,
                                            boxWidth: 5,
                                            boxHeight: 5,
                                        },
                                    },
                                },
                            }
                        });
                    }

                    $('#filterSamplePerYearChart').change(function() {
                        $.ajax({
                            url: '{{ route('admin.dashboard.filter-chart-sample-per-year') }}',
                            type: 'GET',
                            data: {
                                year: $(this).val()
                            },
                            success: function(response) {
                                if (response.length > 0) {
                                    console.log(response);
                                    samplePerYear = response;
                                    updateChart(samplePerYear);
                                } else {
                                    emptyChart();
                                    //
                                }
                            }
                        });
                    });

                    @if (count($samplePerYear) > 0)
                        updateChart(samplePerYear);
                    @endif
                } else {
                    console.error('Canvas element with ID "samplePerYear" not found.');
                }
            });
        </script>

        <script>
            $(function() {
                let sampleAndAbj = @json($sampleAndAbj);

                @if (count($sampleAndAbj) > 0)
                    let orangePalette = [
                        '#f6c23e',
                        '#e74a3b',
                        '#9c27b0'
                    ];

                    // Extract data for labels, total_sample, and total_abj
                    let districtNames = Object.values(sampleAndAbj).map(entry => entry.name);
                    let totalSampleData = Object.values(sampleAndAbj).map(entry => entry.total_sample);
                    let totalAbjData = Object.values(sampleAndAbj).map(entry => entry.total_abj);
                    let totalLarvaData = Object.values(sampleAndAbj).map(entry => entry.total_larva);

                    var ctx = document.getElementById('sampleAndAbj').getContext('2d');
                    // width 100%
                    ctx.canvas.width = '100%';

                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: districtNames,
                            datasets: [{
                                    label: 'Total Sampel',
                                    data: totalSampleData,
                                    backgroundColor: orangePalette[0],
                                    borderColor: orangePalette[0],
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    barPercentage: 0.5,
                                    categoryPercentage: 0.5,
                                },
                                {
                                    label: 'Total ABJ',
                                    data: totalAbjData,
                                    backgroundColor: orangePalette[1],
                                    borderColor: orangePalette[1],
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    barPercentage: 0.5,
                                    categoryPercentage: 0.5,
                                },
                                {
                                    label: 'Total Larva',
                                    data: totalLarvaData,
                                    backgroundColor: orangePalette[2],
                                    borderColor: orangePalette[2],
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    barPercentage: 0.5,
                                    categoryPercentage: 0.5,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            scales: {
                                y: {
                                    grid: {
                                        display: false,
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        precision: 0,
                                        stepSize: 1,
                                    },
                                },
                                x: {
                                    grid: {
                                        display: false,
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        precision: 0,
                                        stepSize: 1,
                                    },
                                },
                            },
                            options: {
                                // ... (other options)
                                plugins: {
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                        callbacks: {
                                            label: function(context) {
                                                var datasetLabel = context.dataset.label || '';
                                                var value = context.parsed.y;
                                                var total = context.dataset.data.reduce(function(
                                                    previousValue, currentValue) {
                                                    return previousValue + currentValue;
                                                });
                                                var percentage = ((value / total) * 100).toFixed(2) + '%';
                                                return datasetLabel + ': ' + percentage;
                                            }
                                        }
                                    },
                                    legend: {
                                        display: true, // Set to true to display the legend
                                        position: 'top', // Change the legend position (e.g., 'top', 'bottom', 'left', 'right')
                                        labels: {
                                            usePointStyle: true,
                                            boxWidth: 5,
                                            boxHeight: 5,
                                            fontColor: 'black', // Change the font color of the legend labels
                                        },
                                    },
                                    // ... (other plugins)
                                },
                            }
                        },
                    });
                @endif

                $('#regency').change(function() {
                    let regencyId = $(this).val();
                    $.ajax({
                        url: '{{ route('admin.dashboard.get-sample-and-abj-by-district') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            regency_id: regencyId
                        },
                        success: function(response) {
                            let sampleAndAbj = response;

                            // Extract data for labels, total_sample, and total_abj
                            let districtNames = Object.values(sampleAndAbj).map(entry => entry
                                .name);
                            let totalSampleData = Object.values(sampleAndAbj).map(entry => entry
                                .total_sample);
                            let totalAbjData = Object.values(sampleAndAbj).map(entry => entry
                                .total_abj);
                            let totalLarvaData = Object.values(sampleAndAbj).map(entry => entry
                                .total_larva);

                            myChart.data.labels = districtNames;
                            myChart.data.datasets[0].data = totalSampleData;
                            myChart.data.datasets[1].data = totalAbjData;
                            myChart.data.datasets[2].data = totalLarvaData;
                            myChart.update();
                        }
                    });
                });

                // set height when canvas already rendered
                $('#sampleAbjCard').height($('#sampleAndAbj').height() + 100);
            });
        </script>
    @endpush
</x-app-layout>
