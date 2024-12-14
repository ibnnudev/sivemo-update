<x-app-layout>
    <x-breadcrumb name="tcases.create" />
    <x-card-container class="xl:w-2/3">
        <form action="{{ route('admin.tcases.store') }}" method="POST">
            @csrf
            <div class="sm:grid grid-cols-2 gap-x-4">
                <div>
                    <p class="text-sm font-semibold mb-6">Detail Sampling</p>
                    <div class="space-y-6">
                        <x-select id="regency_id" label="Kabupaten/Kota" name="regency_id" isFit="true" required>
                            @foreach ($regencies as $regency)
                                <option value="{{ $regency->id }}">{{ $regency->name }}</option>
                            @endforeach
                        </x-select>
                        <x-select id="district_id" label="Kecamatan" name="district_id" isFit="true" required />
                        <x-select id="village_id" label="Desa" name="village_id" isFit="true" required />
                        <p class="text-sm" id="address"></p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-6">Detail Kasus</p>
                    <div class="space-y-6">
                        <x-input id="cases_total" name="cases_total" label="Total Kasus" type="number" required
                            oninput="this.value = Math.abs(this.value)" />
                        <div>
                            <p class="text-xs">Tanggal</p>
                            <div class="relative mt-2">
                                <input name="date" id="date" type="date" required
                                    class="border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-primary focus:border-primary block w-full py-3 px-5"
                                    placeholder="Pilih tanggal mulai" autocomplete="off">
                            </div>
                        </div>

                        <x-select id="vector_type" class="mt-6" label="Type Vector" name="vector_type" isFit="true"
                            required>
                            <option value="Demam Berdarah">Demam Berdarah</option>
                        </x-select>
                    </div>
                </div>

            </div>

            <div class="text-end mt-6">
                <x-button class="bg-primary w-full md:w-auto justify-center">
                    Tambah Kasus
                </x-button>
            </div>
        </form>
    </x-card-container>

    @push('js-internal')
        <script>
            let regency;
            let district;
            let village;



            function removeDetail(id) {
                let count = $('#detailContainer').children().length;
                if (count > 1) {
                    $(`#detail-${id}`).remove();
                }
            }

            $(function() {
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
                            $("#address").text('Alamat: ' + data.address);
                        },
                    });
                });


                @if (Session::has('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: '{{ Session::get('
                                                                                                                                                                                        success ') }}',
                    })
                @endif

                @if (Session::has('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: '{{ Session::get('
                                                                                                                                                                                        error ') }}',
                    })
                @endif
            });
        </script>
    @endpush
</x-app-layout>
