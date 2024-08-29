<x-app-layout>
    <x-breadcrumb name="abj" />
    <x-card-container>
        <div class="z-0 relative mb-4" style="height: 350px; border-radius: 6px;">
            <!-- Legenda -->
            <div class="absolute bottom-0 right-0 p-2 bg-white shadow sm:text-xs text-sm" style="z-index: 2;">
                <h5 class="mb-2 legend-text ">Legend</h5>
                <ul class="list-unstyled">
                    <li>
                        <span class="legend-color legend-green"></span>
                        ABJ Normal
                    </li>
                    <li>
                        <span class="legend-color legend-red"></span>
                        ABJ Tidak Normal
                    </li>
                    <!-- Tambahkan elemen li sesuai dengan legenda Anda -->
                </ul>
            </div>
            <!-- Peta -->
            <div id="map" style="height: 100%; position: relative; z-index: 1;"></div>
        </div>

        <style>
            .legend-color {
                width: 20px;
                height: 20px;
                display: inline-block;
                margin-right: 5px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }

            .legend-green {
                background-color: #1cc88a;
            }

            .legend-yellow {
                background-color: #ffff00;
            }

            .legend-red {
                background-color: #e74a3b;
            }
        </style>
        <!-- <div class="flex flex-col gap-3 md:flex-row md:justify-end mb-4">
            <x-button type="button" data-modal-toggle="defaultModal" color="gray" type="button" class="justify-center">
                Tambah
            </x-button>
        </div> -->
        <table id="abjTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kecamatan</th>
                    <th>Jumlah Sampel</th>
                    <th>Jumlah Pemeriksaan</th>
                    <th>ABJ (%)</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
        </table>
    </x-card-container>

    @push('js-internal')
        <script>
            $("#regency_id").on("change", function() {
                regency = $(this).val();
                $("#district_id").empty();
                $("#village_id").empty();
                $("#district_id").append(
                    `<option value="" selected disabled>Pilih Kecamatan</option>`
                );
                $("#village_id").append(
                    `<option value="" selected disabled>Pilih Desa</option>`
                );
                $.ajax({
                    url: "{{ route('admin.district.list') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        regency_id: regency,
                    },
                    success: function(data) {
                        let districts = Object.values(data);
                        districts.forEach((district) => {
                            $("#district_id").append(
                                `<option value="${district.id}">${district.name}</option>`
                            );
                        });
                    },
                });
            });

            $("#district_id").on("change", function() {
                district = $(this).val();
                $("#village_id").empty();
                $("#village_id").append(
                    `<option value="" selected disabled>Pilih Desa</option>`
                );
                $.ajax({
                    url: "{{ route('admin.village.list') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        district_id: district,
                    },
                    success: function(data) {
                        let villages = Object.values(data);
                        villages.forEach((village) => {
                            $("#village_id").append(
                                `<option value="${village.id}">${village.name}</option>`
                            );
                        });
                    },
                });
            });

            $("#village_id").on("change", function() {
                village = $(this).val();
                $.ajax({
                    url: "{{ route('admin.village.show', ':id') }}".replace(
                        ":id",
                        village
                    ),
                    type: "GET",
                    success: function(data) {
                        $('#address').val(data.address);
                        $("#address").text(data.address);
                    },
                });
            });

            function getColor(abj_total) {
                if (abj_total <= 95) {
                    return '#e74a3b'; // ABJ Sedang
                } else {
                    return '#1cc88a'; // ABJ Rendah1cc88a
                }
            }

            const map = L.map('map').setView([-7.2756196, 112.7106256], 11.5);

            L.tileLayer(
                'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: '&copy; <a href="https://www.mapbox.com/">Mapbox</a> &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
                    maxZoom: 18,
                    id: 'mapbox/light-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: '{{ env('MAPBOX_TOKEN') }}',
                }
            ).addTo(map);

            function updateMapData() {
                // Menggunakan fetch untuk mengambil data GeoJSON dari URL
                let abj = Object.values(@json($abj));
                console.log(abj);
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
            // full screen
            L.control.fullscreen().addTo(map);
        </script>


        <script>
            $(function() {
                $('#abjTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.abj.index') }}",
                    reponsive: true,
                    autoWidth: false,
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'district',
                            name: 'district',
                        },
                        {
                            data: 'total_sample',
                            name: 'total_sample',
                        },
                        {
                            data: 'total_check',
                            name: 'total_check',
                        },
                        {
                            data: 'abj',
                            name: 'abj'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                    ],
                });
            });
        </script>
    @endpush
</x-app-layout>
