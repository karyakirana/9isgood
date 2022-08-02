@php
    $gudang = \App\Models\Master\Gudang::query()
            ->whereNotIn('nama', ['GUDANG BUKU RUSAK'])
            ->get();
    $jenis = collect([['jenis'=>'baik'], ['jenis'=>'rusak']]);
    $stockMasuk = \App\Models\Stock\StockMasuk::query()
            ->where('kondisi')
            ->get();
    $kondisi = collect([['kondisi'=>'baik'], ['kondisi'=>'rusak']]);
@endphp
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="{{route('dashboard')}}">
            <img alt="Logo" src="{{asset('assets/media/logos/default-dark.svg')}}" class="h-25px app-sidebar-logo-default" />
            <img alt="Logo" src="{{asset('assets/media/logos/default-small.svg')}}" class="h-20px app-sidebar-logo-minimize" />
        </a>
        <!--end::Logo image-->
        <!--begin::Sidebar toggle-->
        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
            <span class="svg-icon svg-icon-2 rotate-180">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor" />
										<path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor" />
									</svg>
								</span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Logo-->
    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('dashboard*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
														<rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
														<rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
														<rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Dashboards</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('dashboard') ? 'active' : ''}}" href="{{route('dashboard')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Default</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Master</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('master*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/communication/com005.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M11.8 6.4L16.7 9.2V14.8L11.8 17.6L6.89999 14.8V9.2L11.8 6.4ZM11.8 2C11.5 2 11.2 2.1 11 2.2L3.79999 6.4C3.29999 6.7 3 7.3 3 7.9V16.2C3 16.8 3.29999 17.4 3.79999 17.7L11 21.9C11.3 22.1 11.5 22.1 11.8 22.1C12.1 22.1 12.4 22 12.6 21.9L19.8 17.7C20.3 17.4 20.6 16.8 20.6 16.2V7.9C20.6 7.3 20.3 6.7 19.8 6.4L12.6 2.2C12.4 2.1 12.1 2 11.8 2Z" fill="currentColor"/>
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Master</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('master/produk*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link" href="../../demo1/dist/pages/user-profile/overview.html">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Produk</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion">
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('master/produk') ? 'active' : ''}}" href="{{route('produk')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Data Produk</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('master/produk/kategori') ? 'active' : ''}}" href="{{route('produk.kategori')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Kategori</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('master/produk/kategoriharga') ? 'active' : ''}}" href="{{route('produk.kategoriharga')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Kategori Harga</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                            </div>
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('master/gudang') ? 'active' : ''}}" href="{{route('gudang')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Gudang</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('master/customer') ? 'active' : ''}}" href="{{route('customer')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Customer</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('master/supplier*') ? 'here show' : ''}} menu-accordion">
											<span class="menu-link">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">Supplier</span>
												<span class="menu-arrow"></span>
											</span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('master/supplier') ? 'active' : ''}}" href="{{route('supplier')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Supplier</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('master/supplier/jenis') ? 'active' : ''}}" href="{{route('supplier.jenis')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Supplier Jenis</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('master/pegawai*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/general/gen022.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16.0173 9H15.3945C14.2833 9 13.263 9.61425 12.7431 10.5963L12.154 11.7091C12.0645 11.8781 12.1072 12.0868 12.2559 12.2071L12.6402 12.5183C13.2631 13.0225 13.7556 13.6691 14.0764 14.4035L14.2321 14.7601C14.2957 14.9058 14.4396 15 14.5987 15H18.6747C19.7297 15 20.4057 13.8774 19.912 12.945L18.6686 10.5963C18.1487 9.61425 17.1285 9 16.0173 9Z" fill="currentColor"/>
                                                        <rect opacity="0.3" x="14" y="4" width="4" height="4" rx="2" fill="currentColor"/>
                                                        <path d="M4.65486 14.8559C5.40389 13.1224 7.11161 12 9 12C10.8884 12 12.5961 13.1224 13.3451 14.8559L14.793 18.2067C15.3636 19.5271 14.3955 21 12.9571 21H5.04292C3.60453 21 2.63644 19.5271 3.20698 18.2067L4.65486 14.8559Z" fill="currentColor"/>
                                                        <rect opacity="0.3" x="6" y="5" width="6" height="6" rx="3" fill="currentColor"/>
                                                    </svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Pegawai</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('master/pegawai') ? 'active' : ''}}" href="{{route('pegawai')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Data Pegawai</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item {{request()->is('master/pegawai/user') ? 'active' : ''}}">
                            <!--begin:Menu link-->
                            <a class="menu-link" href="{{route('pegawai.user')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Users</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Transaksi</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('penjualan*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/communication/com013.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z" fill="currentColor" />
														<rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="currentColor" />
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Penjualan</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('penjualan') ? 'active' : ''}}" href="{{route('penjualan')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Penjualan</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('penjualan/trans') ? 'active' : ''}}" href="{{route('penjualan.trans')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Penjualan Baru</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('penjualan/retur/baik') ? 'active' : ''}}" href="{{url('/').'/penjualan/retur/baik'}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Retur Baik</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('penjualan/retur/baik/trans') ? 'active' : ''}}" href="{{url('/').'/penjualan/retur/baik/trans'}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Retur Baik Baru</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('penjualan/retur/rusak') ? 'active' : ''}}" href="{{url('/').'/penjualan/retur/rusak'}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Retur Rusak</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('penjualan/retur/rusak/trans') ? 'active' : ''}}" href="{{url('/').'/penjualan/retur/rusak/trans'}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Retur Rusak Baru</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('pembelian*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/files/fil003.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor" />
														<path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor" />
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Pembelian</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('pembelian') ? 'active' : ''}}" href="{{route('pembelian')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Pembelian</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('pembelian/trans') ? 'active' : ''}}" href="{{route('pembelian.trans')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Pembelian Baru</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('pembelian/retur/baik') ? 'active' : ''}}" href="{{url('/').'/pembelian/retur/baik'}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Retur Baik</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('pembelian/retur/baik/trans') ? 'active' : ''}}" href="{{url('/').'/pembelian/retur/baik/trans'}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Retur Baik Baru</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('pembelian/retur/rusak') ? 'active' : ''}}" href="{{url('/').'/pembelian/retur/rusak'}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Retur Rusak</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{request()->is('pembelian/retur/rusak/trans') ? 'active' : ''}}" href="{{url('/').'/pembelian/retur/rusak/trans'}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Retur Rusak Baru</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Stock Master</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/abstract/abs048.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path opacity="0.3" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor" />
														<path d="M8.70001 6C8.10001 5.7 7.39999 5.40001 6.79999 5.10001C7.79999 4.00001 8.90001 3 10.1 2.2C10.7 2.1 11.4 2 12 2C12.7 2 13.3 2.1 13.9 2.2C12 3.2 10.2 4.5 8.70001 6ZM12 8.39999C13.9 6.59999 16.2 5.30001 18.7 4.60001C18.1 4.00001 17.4 3.6 16.7 3.2C14.4 4.1 12.2 5.40001 10.5 7.10001C11 7.50001 11.5 7.89999 12 8.39999ZM7 20C7 20.2 7 20.4 7 20.6C6.2 20.1 5.49999 19.6 4.89999 19C4.59999 18 4.00001 17.2 3.20001 16.6C2.80001 15.8 2.49999 15 2.29999 14.1C4.99999 14.7 7 17.1 7 20ZM10.6 9.89999C8.70001 8.09999 6.39999 6.9 3.79999 6.3C3.39999 6.9 2.99999 7.5 2.79999 8.2C5.39999 8.6 7.7 9.80001 9.5 11.6C9.8 10.9 10.2 10.4 10.6 9.89999ZM2.20001 10.1C2.10001 10.7 2 11.4 2 12C2 12 2 12 2 12.1C4.3 12.4 6.40001 13.7 7.60001 15.6C7.80001 14.8 8.09999 14.1 8.39999 13.4C6.89999 11.6 4.70001 10.4 2.20001 10.1ZM11 20C11 14 15.4 9.00001 21.2 8.10001C20.9 7.40001 20.6 6.8 20.2 6.2C13.8 7.5 9 13.1 9 19.9C9 20.4 9.00001 21 9.10001 21.5C9.80001 21.7 10.5 21.8 11.2 21.9C11.1 21.3 11 20.7 11 20ZM19.1 19C19.4 18 20 17.2 20.8 16.6C21.2 15.8 21.5 15 21.7 14.1C19 14.7 16.9 17.1 16.9 20C16.9 20.2 16.9 20.4 16.9 20.6C17.8 20.2 18.5 19.6 19.1 19ZM15 20C15 15.9 18.1 12.6 22 12.1C22 12.1 22 12.1 22 12C22 11.3 21.9 10.7 21.8 10.1C16.8 10.7 13 14.9 13 20C13 20.7 13.1 21.3 13.2 21.9C13.9 21.8 14.5 21.7 15.2 21.5C15.1 21 15 20.5 15 20Z" fill="currentColor" />
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Stock</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/inventory*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Stock Report</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/inventory') ? 'active' : ''}}" href="{{route('inventory')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Stock Semua</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                @foreach($gudang as $row)
                                    @foreach($jenis as $item)
                                        <!--begin:Menu item-->
                                        <div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link  {{request()->is('stock/inventory/'.$item['jenis'].'/'.$row->id) ? 'active' : ''}}" href="{{url('/').'/stock/inventory/'.$item['jenis'].'/'.$row->id}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">Stock {{ucwords($row->nama)}} {{ucwords($item['jenis'])}}</span>
                                            </a>
                                            <!--end:Menu link-->
                                        </div>
                                        <!--end:Menu item-->
                                    @endforeach
                                @endforeach
                            </div>
                            <!--end:Menu sub-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/transaksi/masuk*') ? 'here show' : ''}} menu-accordion mb-1">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Stock Masuk</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/masuk') ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/masuk'}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                        <span class="menu-title">Stock Masuk</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                @foreach($kondisi as $item)
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{request()->is('stock/transaksi/masuk/'.$item['kondisi']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/masuk/'.$item['kondisi']}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Stock Masuk {{ucwords($item['kondisi'])}}</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{request()->is('stock/transaksi/masuk/trans/'.$item['kondisi']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/masuk/trans/'.$item['kondisi']}}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                            <span class="menu-title">Stock Masuk {{ucwords($item['kondisi'])}} Baru</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                @endforeach
                            </div>
                            <!--end:Menu sub-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/transaksi/keluar*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Stock Keluar</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                            @foreach($kondisi as $item)
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/keluar/'.$item['kondisi']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/keluar/'.$item['kondisi']}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Stock Keluar {{ucwords($item['kondisi'])}}</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/keluar/trans/'.$item['kondisi']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/keluar/trans/'.$item['kondisi']}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Stock Keluar {{ucwords($item['kondisi'])}} Baru</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                            @endforeach
                            </div>
                            <!--end:Menu sub-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item  {{request()->is('stock/transaksi/opname*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Stock Opname</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion">
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/opname') ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/opname'}}">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                        <span class="menu-title">Stock Opname</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                @foreach($jenis as $item)
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{request()->is('stock/transaksi/opname/'.$item['jenis']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/opname/'.$item['jenis']}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Stock Opname {{ucwords($item['jenis'])}}</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link {{request()->is('stock/transaksi/opname/trans/'.$item['jenis']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/opname/trans/'.$item['jenis']}}">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                            <span class="menu-title">Stock Opname {{ucwords($item['jenis'])}} Baru</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                @endforeach
                            </div>
                            <!--end:Menu sub-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item  {{request()->is('stock/transaksi/mutasi*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Stock Mutasi</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <div class="menu-sub menu-sub-accordion">
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/baik_baik') ? 'active' : ''}}" href="{{route('mutasi.baik_baik')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Mutasi Stock Baik</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/baik_baik/trans') ? 'active' : ''}}" href="{{route('mutasi.baik_baik.trans')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Mutasi Stock Baik Baru</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/baik_rusak') ? 'active' : ''}}" href="{{route('mutasi.baik_rusak')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Mutasi Stock Baik - Rusak</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/baik_rusak/trans') ? 'active' : ''}}" href="{{route('mutasi.baik_rusak.trans')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Mutasi Stock Baik - Rusak Baru</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/rusak_rusak') ? 'active' : ''}}" href="{{route('mutasi.rusak_rusak')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Mutasi Stock Rusak</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/rusak_rusak/trans') ? 'active' : ''}}" href="{{route('mutasi.rusak_rusak.trans')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Mutasi Stock Rusak Baru</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                            </div>
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Keuangan</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z" fill="currentColor" />
														<path opacity="0.3" d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L11 2.225C11.6 1.925 12.4 1.925 13.1 2.225L22 6.725ZM12 3.725C11.2 3.725 10.5 4.425 10.5 5.225C10.5 6.025 11.2 6.725 12 6.725C12.8 6.725 13.5 6.025 13.5 5.225C13.5 4.425 12.8 3.725 12 3.725Z" fill="currentColor" />
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Keuangan</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/master*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Akun</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <div class="menu-sub menu-sub-accordion">
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('keuangan/master/akun') ? 'active' : ''}}" href="{{route('keuangan.master.akun')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Akun</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('keuangan/master/akuntipe') ? 'active' : ''}}" href="{{route('keuangan.master.akuntipe')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Tipe Akun</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('keuangan/master/akunkategori') ? 'active' : ''}}" href="{{route('keuangan.master.akunkategori')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Kategori Akun</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('keuangan/master/rekanan') ? 'active' : ''}}" href="{{route('keuangan.master.rekanan')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                        <span class="menu-title">Rekanan</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                            </div>
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/neraca/*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Neraca</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <!--begin:Menu item-->
                                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/neraca/awal') ? 'here show' : ''}} menu-accordion">
                                    <!--begin:Menu link-->
                                    <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Neraca Awal</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <!--end:Menu link-->
                                    <!--begin:Menu sub-->
                                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                                        <!--begin:Menu item-->
                                        <div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link {{request()->is('keuangan/master/akuntipe') ? 'active' : ''}}" href="{{route('keuangan.master.akuntipe')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                                <span class="menu-title">Piutang</span>
                                            </a>
                                            <!--end:Menu link-->
                                        </div>
                                        <!--end:Menu item-->
                                        <!--begin:Menu item-->
                                        <div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link {{request()->is('keuangan/master/akuntipe') ? 'active' : ''}}" href="{{route('keuangan.master.akuntipe')}}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                                <span class="menu-title">Hutang</span>
                                            </a>
                                            <!--end:Menu link-->
                                        </div>
                                        <!--end:Menu item-->
                                    </div>
                                    <!--begin:Menu sub-->
                                </div>
                                <!--end:Menu item-->
                            </div>
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan//*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Neraca</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('kasir/*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z" fill="currentColor" />
														<path opacity="0.3" d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L11 2.225C11.6 1.925 12.4 1.925 13.1 2.225L22 6.725ZM12 3.725C11.2 3.725 10.5 4.425 10.5 5.225C10.5 6.025 11.2 6.725 12 6.725C12.8 6.725 13.5 6.025 13.5 5.225C13.5 4.425 12.8 3.725 12 3.725Z" fill="currentColor" />
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Kasir</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <!--begin:Menu item-->
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('kasir/penerimaan/*') ? 'here show' : ''}} menu-accordion">
                            <!--begin:Menu link-->
                            <span class="menu-link">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                <span class="menu-title">Penerimaan</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <!--end:Menu link-->
                            <!--begin:Menu sub-->
                            <div class="menu-sub menu-sub-accordion">
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('kasir/penerimaan/penjualan') ? 'active' : ''}}" href="{{route('kasir.penerimaan.penjualan')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Penerimaan Penjualan</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('kasir/penerimaan/penjualan/baru') ? 'active' : ''}}" href="{{route('kasir.penerimaan.penjualan.baru')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Penerimaan Penjualan Baru</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link {{request()->is('kasir/penerimaan/piutangpenjualan*') ? 'active' : ''}}" href="{{route('kasir.piutang.penjualan')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                        <span class="menu-title">Piutang</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                <!--end:Menu item-->
                            </div>
                        </div>
                        <!--end:Menu item-->
                    </div>
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('config*') ? 'here show' : ''}} menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
											<span class="menu-icon">
												<!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z" fill="currentColor" />
														<path opacity="0.3" d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L11 2.225C11.6 1.925 12.4 1.925 13.1 2.225L22 6.725ZM12 3.725C11.2 3.725 10.5 4.425 10.5 5.225C10.5 6.025 11.2 6.725 12 6.725C12.8 6.725 13.5 6.025 13.5 5.225C13.5 4.425 12.8 3.725 12 3.725Z" fill="currentColor" />
													</svg>
												</span>
                                                <!--end::Svg Icon-->
											</span>
											<span class="menu-title">Config</span>
											<span class="menu-arrow"></span>
										</span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('config/akun') ? 'active' : ''}}" href="{{route('config')}}">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
                                    <span class="menu-title">Config Akun</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                </div>
                <!--end:Menu item-->
            </div>
            <!--end::Menu-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
