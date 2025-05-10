@extends('layouts.admin')
@section('page-title', 'Dasbor')

@section('content')

    <div class="row mb-4">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Selamat Datang {{ Auth::user()->name }} 🎉</h5>
                            <p class="mb-4">
                                Aplikasimu hari ini naik pesat dibanding sebelumnya.
                                Cek statistik terbarumu sekarang! 🚀
                            </p>

                            <a href="#chart" class="btn btn-sm btn-outline-primary">Lihat Statistik!</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="../admin/assets/img/illustrations/man-with-laptop-light.png" height="140"
                                alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                data-app-light-img="illustrations/man-with-laptop-light.png" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mt-4">
                <div class="card p-4">
                    <div class="card-body">
                        <div id="chart" style="height: 390.9px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                            <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                <div class="card-title">
                                    <h5 class="text-nowrap mb-2">Total Aktivitas</h5>
                                    <span class="badge bg-label-warning rounded-pill">Keseluruhan</span>
                                </div>
                                <div class="mt-sm-auto d-flex gap-2">
                                    <h3 class="mb-0">{{ $total }}</h3>
                                    <small style="position: relative; top: 5px;" class="text-muted">aktivitas</small>
                                </div>
                            </div>
                            <div id="profileReportChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Data Peminjaman / Pengembalian</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column gap-1" style="position: relative; bottom: 15px;">
                            <span>Peminjaman <strong>{{ $peminjaman }}</strong></span>
                            <span>Pengembalian <strong>{{ $pengembalian }}</strong></span>
                        </div>
                        <div id="orderStatisticsChart"></div>
                    </div>
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="../admin/assets/img/gif-icons/pinjam.png" alt="">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Peminjaman</h6>
                                    <small class="text-muted">Total peminjaman barang</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ $peminjaman }}x</small>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="../admin/assets/img/gif-icons/kembali.png" alt="">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Pengembalian</h6>
                                    <small class="text-muted">Total pengembalian barang</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ $pengembalian }}x</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="../admin/assets/img/gif-icons/barang-masuk.png" alt="chart success"
                                        class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="{{ route('brg-masuk.index') }}">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Barang Masuk</span>
                            <small class="text-muted">total stok</small>
                            <h3 class="card-title mb-2">{{ $totalStokMasuk }}</h3>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="../admin/assets/img/gif-icons/petugas.png" alt="chart success"
                                        class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="{{ route('karyawan.index') }}">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Barang Keluar</span>
                            <small class="text-muted">total stok</small>
                            <h3 class="card-title mb-2">{{ $totalStokKeluar }}</h3>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        //statistik semua data
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Jumlah',
                    data: {!! json_encode($chartData['series']) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($chartData['labels']) !!},
                    labels: {
                        style: {
                            fontSize: '10px',
                            colors: '#6B7280'
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        borderRadiusApplication: 'end',
                        borderRadiusWhenStacked: 'last',
                        distributed: true,
                        columnWidth: '55%',
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '13px',
                        colors: ['#fff']
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + ' data';
                        }
                    }
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    itemMargin: {
                        horizontal: 10,
                        vertical: 8 // memberi jarak antar item legend
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        });

        //statistik total aktivitas
        const profileReportChartEl = document.querySelector('#profileReportChart'),
            profileReportChartConfig = {
                chart: {
                    height: 80,
                    // width: 175,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                    dropShadow: {
                        enabled: true,
                        top: 10,
                        left: 5,
                        blur: 3,
                        color: config.colors.warning,
                        opacity: 0.15
                    },
                    sparkline: {
                        enabled: true
                    }
                },
                grid: {
                    show: false,
                    padding: {
                        right: 8
                    }
                },
                colors: [config.colors.warning],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 5,
                    curve: 'smooth'
                },
                series: [{
                    name: 'Jumlah',
                    data: {!! json_encode($chartData['series']) !!}
                }],
                xaxis: {
                    categories: {!! json_encode($chartData['labels']) !!},
                    labels: {
                        style: {
                            fontSize: '10px',
                            colors: '#6B7280'
                        }
                    }
                },
                yaxis: {
                    show: false
                }
            };
        if (typeof profileReportChartEl !== undefined && profileReportChartEl !== null) {
            const profileReportChart = new ApexCharts(profileReportChartEl, profileReportChartConfig);
            profileReportChart.render();
        }


        //statistik peminjaman
        document.addEventListener('DOMContentLoaded', function() {
            const config = {
                colors: {
                    primary: '#3B82F6',
                    secondary: '#64748B',
                    info: '#0EA5E9',
                    success: '#10B981',
                    danger: '#EF4444'
                }
            };
            const cardColor = '#fff';
            const headingColor = '#111827';
            const axisColor = '#6B7280';

            const chartOrderStatistics = document.querySelector('#orderStatisticsChart');
            const orderChartConfig = {
                chart: {
                    height: 165,
                    width: 130,
                    type: 'donut'
                },
                labels: {!! json_encode($chartData['pinjamkembali']) !!},
                series: {!! json_encode($chartData['pinjamkembaliseries']) !!},
                colors: [config.colors.success, config.colors.danger],
                stroke: {
                    width: 5,
                    colors: cardColor
                },
                dataLabels: {
                    enabled: false,
                    formatter: function(val, opt) {
                        return parseInt(val) + '%';
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    padding: {
                        top: 0,
                        bottom: 0,
                        right: 15
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '85%',
                            labels: {
                                show: true,
                                value: {
                                    fontSize: '1.5rem',
                                    fontFamily: 'Public Sans',
                                    color: headingColor,
                                    offsetY: -15,
                                    formatter: function(val) {
                                        return parseInt(val);
                                    }
                                },
                                name: {
                                    offsetY: 20,
                                    fontFamily: 'Public Sans'
                                },
                                total: {
                                    show: true,
                                    fontSize: '0.8125rem',
                                    color: axisColor,
                                    label: 'Total',
                                    formatter: function(w) {
                                        return '{{ $peminjaman + $pengembalian }}';
                                    }
                                }
                            }
                        }
                    }
                }
            };

            if (chartOrderStatistics !== null) {
                const statisticsChart = new ApexCharts(chartOrderStatistics, orderChartConfig);
                statisticsChart.render();
            }
        });
    </script>


@endsection
