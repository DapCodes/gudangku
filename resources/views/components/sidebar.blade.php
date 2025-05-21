        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo mb-2 mt-2" style="position: relative; right: 10px;">
                <a href="index.html" class="app-brand-link">
                    <img style="width: 200px" src="{{ asset('admin/assets/img/icons/brands/gudangku-icon.png') }}"
                        alt="">
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                    <i class="bx bx-chevron-left bx-sm align-middle"></i>
                </a>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner py-1 gap-1">
                <!-- Dashboard -->
                <!-- Dashboard -->
                <li class="menu-item {{ Request::is('admin/home*') ? 'active' : '' }}">
                    <a href="{{ route('admin.home') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div>Dasbor</div>
                    </a>
                </li>
                <!-- Components -->
                <li class="menu-header small text-uppercase mb-3"><span class="menu-header-text">Mengelola</span></li>
                <!-- Cards -->
                @if (Auth::user()->is_admin == 1)
                    <!-- Data Karyawan -->
                    <li class="menu-item {{ Request::is('admin/karyawan*') ? 'active' : '' }}">
                        <a href="{{ route('karyawan.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div>Data Petugas</div>
                        </a>
                    </li>
                @endif
                <!-- Data Barang -->
                <li class="menu-item {{ Request::is('admin/barang*') ? 'active' : '' }}">
                    <a href="{{ route('barang.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div>Data Barang</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::is('admin/ruangan*') ? 'active' : '' }}">
                    <a href="{{ route('ruangan.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div>Data Ruangan</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::is('admin/brg-ruangan*') ? 'active' : '' }}">
                    <a href="{{ route('brg-ruangan.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div>Data Barang Ruangan</div>
                    </a>
                </li>
                <!-- Barang Masuk -->
                <li class="menu-item {{ Request::is('admin/brg-masuk*') ? 'active' : '' }}">
                    <a href="{{ route('brg-masuk.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-log-in"></i>
                        <div>Barang Masuk</div>
                    </a>
                </li>

                <!-- Barang Keluar -->
                <li class="menu-item {{ Request::is('admin/brg-keluar*') ? 'active' : '' }}">
                    <a href="{{ route('brg-keluar.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-log-out"></i>
                        <div>Barang Keluar</div>
                    </a>
                </li>

                <!-- Data Peminjaman -->
                <li class="menu-item {{ Request::is('admin/peminjaman*') ? 'active' : '' }}">
                    <a href="{{ route('peminjaman.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-download"></i>
                        <div>Data Peminjaman</div>
                    </a>
                </li>

                <!-- Data Pengembalian -->
                <li class="menu-item {{ Request::is('admin/pengembalian*') ? 'active' : '' }}">
                    <a href="{{ route('pengembalian.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-undo"></i>
                        <div>Data Pengembalian</div>
                    </a>
                </li>
            </ul>

        </aside>

