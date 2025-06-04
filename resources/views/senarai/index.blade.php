@extends('layouts.master')

@section('page_title', 'Senarai Aduan')

@push('styles')
<style>
    .table-responsive thead.sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .table-responsive thead.shadow {
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    #senaraiTable {
        margin-bottom: 0;
    }
    #senaraiTable tbody tr {
        transition: background-color 0.2s;
    }    #senaraiTable tbody tr:hover {
        background-color: rgba(0,123,255,0.1);
    }

    /* Enhanced styles for clickable senarai rows */
    .senarai-row:hover {
        background-color: rgba(0,123,255,0.15) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }

    .senarai-row:active {
        transform: translateY(0);
    }
    #senaraiTable th {
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 2px solid #dee2e6;
    }    .badge {
        font-size: 90%;
    }    .badge-orange {
        background-color: #fd7e14;
        color: #fff;
    }
    .btn-group .btn {
        padding: .2rem .4rem;
    }
    /* Allow text wrapping in content cells, but not in headers */
    
    th {
        white-space: nowrap;
    }
    
    /* Gaya untuk bullet points */
    td ul {
        margin-bottom: 0;
        padding-left: 1.2rem;
    }
    td ul li {
        font-size: 0.9rem;
        line-height: 1.3;
        margin-bottom: 0.2rem;
    }
    td ul li:last-child {
        margin-bottom: 0;
    }
      #clearSearch {
        background: transparent;
        border: none;
        cursor: pointer;
        z-index: 10;
        right: 50px;
        top: 2px;
        color: #6c757d;
        opacity: 0.7;
        box-shadow: none;
        padding: 0.375rem 0.4rem;
        transition: all 0.2s ease;
    }
    #clearSearch:hover {
        color: #dc3545;
        opacity: 1;
    }
    #clearSearch:focus {
        box-shadow: none;
        outline: none;
    }    .btn-transparent {
        background-color: transparent;
        border-color: transparent;
    }    /* Modal detail alignment styles */
    .detail-label {
        display: inline-block;
        width: 110px;
        font-weight: bold;
        vertical-align: top;
    }
    
    .detail-label-wide {
        display: inline-block;
        width: 190px;
        font-weight: bold;
        vertical-align: top;
    }
    
    .detail-value {
        font-weight: normal;
    }
    
    /* Compact modal styles */
    #detailSenaraiModal .modal-dialog {
        max-width: 800px;
    }

    #detailSenaraiModal .form-group {
        margin-bottom: 0.5rem;
    }

    #detailSenaraiModal .modal-body {
        padding: 1rem;
    }

    #detailSenaraiModal .row {
        margin-bottom: 0.75rem;
    }
    
    /* PPK text wrap dalam modal detail */
    #detailPpk {
        word-break: break-word;
        white-space: normal;
        line-height: 1.4;
        max-width: 250px;
        display: inline-block;
    }
</style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Senarai Aduan</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Senarai Aduan</h3>
            <div class="card-tools">                
                <a href="{{ route('senarai.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah Aduan</span>
                </a>                                
                    <button type="button" class="btn btn-warning btn-sm ml-2" data-toggle="modal" data-target="#importModal">
                    <i class="fas fa-file-import"></i> <span class="d-none d-sm-inline">Import</span>

                    <button type="button" class="btn btn-success btn-sm ml-2 dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-file-export"></i> <span class="d-none d-sm-inline">Export</span>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('senarai.export', ['format' => 'xlsx']) }}">Excel</a>
                        <a class="dropdown-item" href="{{ route('senarai.export', ['format' => 'CSV']) }}">CSV</a>
                        <a class="dropdown-item" href="{{ route('senarai.print') }}" target="_blank">PDF</a>
                    </div>
                <a href="{{ route('senarai.print.html') }}" class="btn btn-secondary btn-sm ml-2" target="_blank">
                    <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Cetak</span>
                </a>
            </div>
        </div>
    </div>    <div class="card-body">        
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group position-relative">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari senarai..." value="{{ request('search') }}">
                    <button type="button" id="clearSearch" class="btn btn-transparent position-absolute" title="Kosongkan carian">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-bordered table-striped table-sm" id="senaraiTable">
                <thead class="sticky-top bg-white">
                    <tr>
                        <th class="text-center align-middle">Bil.</th>
                        <th class="text-center align-middle">Tarikh Aduan</th>
                        <th class="text-center align-middle">Cawangan</th>
                        <th class="text-center align-middle">No. Siri</th>
                        <th class="text-center align-middle">Model</th>
                        <th class="text-center align-middle">Aduan</th>
                        <th class="text-center align-middle">Vendor</th>
                        <th class="text-center align-middle">Tarikh Selesai Baikpulih</th>
                        <th class="text-center align-middle">Tarikh Hantar Cawangan</th>
                        <th class="text-center align-middle">Status</th>
                        <th class="text-center align-middle">Tindakan</th>
                    </tr>
                </thead>
                <tbody>                   
                     @forelse ($senarais as $index => $senarai)                    
                     <tr class="senarai-row" style="cursor: pointer;"                        
                        data-id="{{ $senarai->id }}"
                        data-tarikh="{{ $senarai->tarikh_aduan->format('d/m/Y') }}"
                        data-ppk="{{ $senarai->ppk_name ?? '-' }}"
                        data-cawangan="{{ $senarai->cawangan_name ?? '-' }}"
                        data-no-siri="{{ $senarai->no_siri }}"
                        data-peralatan="{{ $senarai->peralatan_name ?? '-' }}"
                        data-modelan="{{ $senarai->modelan_name ?? '-' }}"
                        data-vendor="{{ $senarai->vendor_name ?? '-' }}"
                        data-tarikh-hantar-baikpulih="{{ $senarai->tarikh_hantar_baikpulih ? $senarai->tarikh_hantar_baikpulih->format('d/m/Y') : '-' }}"
                        data-tarikh-selesai-baikpulih="{{ $senarai->tarikh_selesai_baikpulih ? $senarai->tarikh_selesai_baikpulih->format('d/m/Y') : '-' }}"
                        data-tarikh-hantar-cawangan="{{ $senarai->tarikh_hantar_cawangan ? $senarai->tarikh_hantar_cawangan->format('d/m/Y') : '-' }}"
                        data-kos="{{ $senarai->kos ? 'RM ' . number_format($senarai->kos, 2) : '-' }}"
                        data-status="{{ $senarai->status->nama }}"
                        data-aduan="{{ $senarai->aduan ?? '-' }}"
                        data-penyelesaian="{{ $senarai->penyelesaian ?? '-' }}"
                        data-catatan="{{ $senarai->catatan ?? '-' }}">                        
                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                        <td class="text-center align-middle">{{ $senarai->tarikh_aduan->format('d/m/Y') }}</td>
                        <td class="text-center align-middle">{{ $senarai->cawangan_name ?? '-' }}</td>                        
                        <td class="text-center align-middle">{{ $senarai->no_siri }}</td>
                        <td class="text-center align-middle">{{ $senarai->modelan_name ?? '-' }}</td>
                        <td class="align-middle">
                            <ul class="pl-3 mb-0">
                                @foreach ($senarai->aduanArray as $item)
                                    @if(trim($item) !== '')
                                        <li>{{ trim($item) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </td>
                        <td class="text-center align-middle">{{ $senarai->vendor_name ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $senarai->tarikh_selesai_baikpulih ? $senarai->tarikh_selesai_baikpulih->format('d/m/Y') : '-' }}</td>
                        <td class="text-center align-middle">{{ $senarai->tarikh_hantar_cawangan ? $senarai->tarikh_hantar_cawangan->format('d/m/Y') : '-' }}</td>
                        <td class="text-center align-middle">
                            <span class="badge badge-{{
                                strtolower($senarai->status->nama) == 'selesai' ? 'success' :
                                (strtolower($senarai->status->nama) == 'tersedia' ? 'primary' :
                                (strtolower($senarai->status->nama) == 'dibaikpulih' ? 'warning' :
                                (strtolower($senarai->status->nama) == 'proses' ? 'orange' :
                                (strtolower($senarai->status->nama) == 'baru' ? 'danger' : 'secondary'))))
                            }}">
                                {{ $senarai->status->nama }}
                            </span>
                        </td>                        
                        <td class="text-center align-middle">
                            <div class="btn-group">

                                <a href="{{ route('senarai.edit', $senarai) }}" class="btn btn-sm btn-warning" onclick="event.stopPropagation();">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $senarai->id }}" onclick="event.stopPropagation();">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            
                            <!-- Modal Delete -->
                            <div class="modal fade" id="deleteModal{{ $senarai->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel">Pengesahan Hapus</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Adakah anda pasti ingin menghapus rekod ini?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <form action="{{ route('senarai.destroy', $senarai) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>                    
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Tiada data aduan dijumpai</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>        
        </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <small class="text-muted">Menunjukkan <span id="visible-records">{{ count($senarais) }}</span> dari <span id="total-records">{{ count($senarais) }}</span> rekod</small>
            </div>
        </div>
    </div>
</div>
<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Aduan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('senarai.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle mr-1"></i> Panduan Import:</h6>
                        <ol class="pl-3 mb-0">
                            <li>Pastikan data dalam format Excel (.xlsx) atau CSV (.csv)</li>
                            <li>Gunakan template yang disediakan untuk format terbaik</li>
                            <li>Kolom bertanda * adalah wajib diisi</li>
                        </ol>
                    </div>
                    
                    <div class="form-group mt-3">
                        <a href="{{ route('senarai.template.download') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-download mr-1"></i> Muat Turun Template
                        </a>
                    </div>
                    
                    <div class="form-group">
                        <label for="file">Pilih Fail Excel/CSV</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('file') is-invalid @enderror" id="file" name="file" required>
                                <label class="custom-file-label" for="file">Pilih fail...</label>
                            </div>
                        </div>
                        @error('file')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import mr-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Aduan -->
<div class="modal fade" id="detailSenaraiModal" tabindex="-1" role="dialog" aria-labelledby="detailSenaraiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailSenaraiModalLabel">Detail Senarai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <span class="detail-label">Tarikh Aduan:</span>
                            <span id="detailTarikhAduan" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label">Cawangan:</span>
                            <span id="detailCawangan" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label">PPK:</span>
                            <span id="detailPpk" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label">Peralatan:</span>
                            <span id="detailPeralatan" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label">Model:</span>
                            <span id="detailModel" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label">No. Siri:</span>
                            <span id="detailNoSiri" class="detail-value">-</span>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <span class="detail-label-wide">Vendor:</span>
                            <span id="detailVendor" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label-wide">Tarikh Hantar Baikpulih:</span>
                            <span id="detailTarikhHantarBaikpulih" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label-wide">Tarikh Selesai Baikpulih:</span>
                            <span id="detailTarikhSelesaiBaikpulih" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label-wide">Tarikh Hantar Cawangan:</span>
                            <span id="detailTarikhHantarCawangan" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label-wide">Kos:</span>
                            <span id="detailKos" class="detail-value">-</span>
                        </div>
                        <div class="form-group">
                            <span class="detail-label-wide">Status:</span>
                            <span id="detailStatus" class="detail-value">-</span>
                        </div>
                    </div>
                </div>                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Aduan:</label>
                            <div id="detailAduan" class="form-control-plaintext border p-2 bg-light" style="min-height: 40px; font-size: 0.9rem;">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Penyelesaian:</label>
                            <div id="detailPenyelesaian" class="form-control-plaintext border p-2 bg-light" style="min-height: 40px; font-size: 0.9rem;">-</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Catatan:</label>
                    <div id="detailCatatan" class="form-control-plaintext border p-2 bg-light" style="min-height: 30px; font-size: 0.9rem;">-</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a href="#" id="editAduanBtn" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Aduan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show file name when a file is selected
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });        // Function to perform search
        function performSearch() {
            var value = $("#searchInput").val().toLowerCase();
            var visibleCount = 0;
            var totalCount = $("#senaraiTable tbody tr").length;

            $("#senaraiTable tbody tr").filter(function() {
                var matches = $(this).text().toLowerCase().indexOf(value) > -1;
                if (matches) visibleCount++;
                return $(this).toggle(matches);
            });
            
            // Update counter
            $("#visible-records").text(visibleCount);
            $("#total-records").text(totalCount);
            
            // Show/hide clear button based on search input
            if (value.length > 0) {
                $("#clearSearch").show();
            } else {
                $("#clearSearch").hide();
            }
        }
        
        // Real-time search functionality
        $("#searchInput").on("keyup", performSearch);
        
        // Clear search button functionality
        $("#clearSearch").on("click", function() {
            $("#searchInput").val("");
            performSearch();
            $(this).hide();
        });
        
        // Initialize - hide clear button if no search text
        if ($("#searchInput").val().length === 0) {
            $("#clearSearch").hide();
        }

        // Maintain sticky header during scroll
        $('.table-responsive').scroll(function() {
            var scrollTop = $(this).scrollTop();
            if (scrollTop > 0) {
                $('thead').addClass('shadow');
            } else {
                $('thead').removeClass('shadow');
            }
        });
          // Handle row click for detail modal
        $(document).on('click', '.senarai-row', function() {
            var $row = $(this);
              // Populate modal with row data
            $('#detailTarikhAduan').text($row.data('tarikh'));
            $('#detailCawangan').text($row.data('cawangan'));
            $('#detailPpk').text($row.data('ppk'));
            $('#detailPeralatan').text($row.data('peralatan'));
            $('#detailModel').text($row.data('modelan'));
            $('#detailNoSiri').text($row.data('no-siri'));
            $('#detailVendor').text($row.data('vendor'));
            $('#detailTarikhHantarBaikpulih').text($row.data('tarikh-hantar-baikpulih'));
            $('#detailTarikhSelesaiBaikpulih').text($row.data('tarikh-selesai-baikpulih'));
            $('#detailTarikhHantarCawangan').text($row.data('tarikh-hantar-cawangan'));
            $('#detailKos').text($row.data('kos'));
            
            // Display status with proper color badge
            var status = $row.data('status');
            var badgeClass = 'secondary';
            if (status.toLowerCase() == 'selesai') badgeClass = 'success';
            else if (status.toLowerCase() == 'tersedia') badgeClass = 'primary';
            else if (status.toLowerCase() == 'dibaikpulih') badgeClass = 'warning';
            else if (status.toLowerCase() == 'proses') badgeClass = 'orange';
            else if (status.toLowerCase() == 'baru') badgeClass = 'danger';
            $('#detailStatus').html('<span class="badge badge-' + badgeClass + '">' + status + '</span>');
            
            // Format aduan as list
            var aduanList = $row.data('aduan').split(';').filter(function(item) {
                return item.trim() !== '';
            });
            var aduanHtml = '<ul class="pl-3 mb-0">';
            aduanList.forEach(function(item) {
                aduanHtml += '<li>' + item.trim() + '</li>';
            });
            aduanHtml += '</ul>';
            $('#detailAduan').html(aduanHtml);
            
            // Format penyelesaian as bullet points
            var penyelesaian = $row.data('penyelesaian');
            if (penyelesaian && penyelesaian !== '-') {
                var penyelesaianList = penyelesaian.split(';').filter(function(item) {
                    return item.trim() !== '';
                });
                if (penyelesaianList.length > 1) {
                    var penyelesaianHtml = '<ul class="pl-3 mb-0">';
                    penyelesaianList.forEach(function(item) {
                        penyelesaianHtml += '<li>' + item.trim() + '</li>';
                    });
                    penyelesaianHtml += '</ul>';
                    $('#detailPenyelesaian').html(penyelesaianHtml);
                } else {
                    $('#detailPenyelesaian').text(penyelesaian);
                }
            } else {
                $('#detailPenyelesaian').text('-');
            }
            
            $('#detailCatatan').text($row.data('catatan'));
            
            // Set edit button URL
            $('#editSenaraiBtn').attr('href', '{{ route("senarai.edit", ":id") }}'.replace(':id', $row.data('id')));

            // Show modal
            $('#detailSenaraiModal').modal('show');
        });
    });
    
    // Show import modal if there's an error with the import
    @if($errors->has('file'))
        $('#importModal').modal('show');
    @endif
    
</script>
@endpush