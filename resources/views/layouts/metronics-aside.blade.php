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
<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="{{route('dashboard')}}">
            <img alt="Logo" src="{{asset('assets/media/logos/logo-1-dark.svg')}}" class="h-25px logo" />
        </a>
        <!--end::Logo-->
        <!--begin::Aside toggler-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
            <span class="svg-icon svg-icon-1 rotate-180">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="black" />
									<path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="black" />
								</svg>
							</span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Aside toggler-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid">
        <!--begin::Aside Menu-->
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
            <!--begin::Menu-->
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('dashboard*') ? 'here show' : ''}} menu-accordion">
									<span class="menu-link">
										<span class="menu-icon">
											<!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
											<span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
													<rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
													<rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
													<rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
												</svg>
											</span>
                                            <!--end::Svg Icon-->
										</span>
										<span class="menu-title">Dashboards</span>
										<span class="menu-arrow"></span>
									</span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('dashboard') ? 'active' : ''}} " href="{{route('dashboard')}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Index</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Master</span>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('master*') ? 'here show' : ''}} menu-accordion">
									<span class="menu-link">
										<span class="menu-icon">
											<!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm007.svg-->
											<span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M21 2H13C12.4 2 12 2.4 12 3V13C12 13.6 12.4 14 13 14H21C21.6 14 22 13.6 22 13V3C22 2.4 21.6 2 21 2ZM15.7 8L14 10.1V5.80005L15.7 8ZM15.1 4H18.9L17 6.40002L15.1 4ZM17 9.59998L18.9 12H15.1L17 9.59998ZM18.3 8L20 5.90002V10.2L18.3 8ZM9 2H3C2.4 2 2 2.4 2 3V21C2 21.6 2.4 22 3 22H9C9.6 22 10 21.6 10 21V3C10 2.4 9.6 2 9 2ZM4.89999 12L4 14.8V9.09998L4.89999 12ZM4.39999 4H7.60001L6 8.80005L4.39999 4ZM6 15.2L7.60001 20H4.39999L6 15.2ZM7.10001 12L8 9.19995V14.9L7.10001 12Z" fill="black"/>
                                                    <path d="M21 18H13C12.4 18 12 17.6 12 17C12 16.4 12.4 16 13 16H21C21.6 16 22 16.4 22 17C22 17.6 21.6 18 21 18ZM19 21C19 20.4 18.6 20 18 20H13C12.4 20 12 20.4 12 21C12 21.6 12.4 22 13 22H18C18.6 22 19 21.6 19 21Z" fill="black"/>
                                                </svg>
											</span>
                                            <!--end::Svg Icon-->
										</span>
										<span class="menu-title">Master</span>
										<span class="menu-arrow"></span>
									</span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('master/produk*') ? 'here show' : ''}} menu-accordion">
											<span class="menu-link">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">Produk</span>
												<span class="menu-arrow"></span>
											</span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('master/produk') ? 'active' : ''}}" href="{{route('produk')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Produk</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('master/produk/kategori') ? 'active' : ''}}" href="{{route('produk.kategori')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Kategori Produk</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('master/produk/kategoriharga') ? 'active' : ''}}" href="{{route('produk.kategoriharga')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Kategori Harga Produk</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('master/gudang') ? 'active' : ''}}" href="{{route('gudang')}}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Gudang</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('master/customer') ? 'active' : ''}}" href="{{route('customer')}}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Customer</span>
                            </a>
                        </div>
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
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('master/pegawai*') ? 'here show' : ''}} menu-accordion">
									<span class="menu-link">
										<span class="menu-icon">
											<!--begin::Svg Icon | path: icons/duotune/communication/com013.svg-->
											<span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z" fill="black" />
													<rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="black" />
												</svg>
											</span>
                                            <!--end::Svg Icon-->
										</span>
										<span class="menu-title">Pegawai</span>
										<span class="menu-arrow"></span>
									</span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('master/pegawai') ? 'active' : ''}}" href="{{route('pegawai')}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Pegawai</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('master/pegawai/user') ? 'active' : ''}}" href="{{route('pegawai.user')}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Users</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Transaction</span>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('penjualan*') ? 'here show' : ''}} menu-accordion">
									<span class="menu-link">
										<span class="menu-icon">
											<!--begin::Svg Icon | path: icons/duotune/general/gen002.svg-->
											<span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path opacity="0.3" d="M4.05424 15.1982C8.34524 7.76818 13.5782 3.26318 20.9282 2.01418C21.0729 1.98837 21.2216 1.99789 21.3618 2.04193C21.502 2.08597 21.6294 2.16323 21.7333 2.26712C21.8372 2.37101 21.9144 2.49846 21.9585 2.63863C22.0025 2.7788 22.012 2.92754 21.9862 3.07218C20.7372 10.4222 16.2322 15.6552 8.80224 19.9462L4.05424 15.1982ZM3.81924 17.3372L2.63324 20.4482C2.58427 20.5765 2.5735 20.7163 2.6022 20.8507C2.63091 20.9851 2.69788 21.1082 2.79503 21.2054C2.89218 21.3025 3.01536 21.3695 3.14972 21.3982C3.28408 21.4269 3.42387 21.4161 3.55224 21.3672L6.66524 20.1802L3.81924 17.3372ZM16.5002 5.99818C16.2036 5.99818 15.9136 6.08615 15.6669 6.25097C15.4202 6.41579 15.228 6.65006 15.1144 6.92415C15.0009 7.19824 14.9712 7.49984 15.0291 7.79081C15.0869 8.08178 15.2298 8.34906 15.4396 8.55884C15.6494 8.76862 15.9166 8.91148 16.2076 8.96935C16.4986 9.02723 16.8002 8.99753 17.0743 8.884C17.3484 8.77046 17.5826 8.5782 17.7474 8.33153C17.9123 8.08486 18.0002 7.79485 18.0002 7.49818C18.0002 7.10035 17.8422 6.71882 17.5609 6.43752C17.2796 6.15621 16.8981 5.99818 16.5002 5.99818Z" fill="black" />
													<path d="M4.05423 15.1982L2.24723 13.3912C2.15505 13.299 2.08547 13.1867 2.04395 13.0632C2.00243 12.9396 1.9901 12.8081 2.00793 12.679C2.02575 12.5498 2.07325 12.4266 2.14669 12.3189C2.22013 12.2112 2.31752 12.1219 2.43123 12.0582L9.15323 8.28918C7.17353 10.3717 5.4607 12.6926 4.05423 15.1982ZM8.80023 19.9442L10.6072 21.7512C10.6994 21.8434 10.8117 21.9129 10.9352 21.9545C11.0588 21.996 11.1903 22.0083 11.3195 21.9905C11.4486 21.9727 11.5718 21.9252 11.6795 21.8517C11.7872 21.7783 11.8765 21.6809 11.9402 21.5672L15.7092 14.8442C13.6269 16.8245 11.3061 18.5377 8.80023 19.9442ZM7.04023 18.1832L12.5832 12.6402C12.7381 12.4759 12.8228 12.2577 12.8195 12.032C12.8161 11.8063 12.725 11.5907 12.5653 11.4311C12.4057 11.2714 12.1901 11.1803 11.9644 11.1769C11.7387 11.1736 11.5205 11.2583 11.3562 11.4132L5.81323 16.9562L7.04023 18.1832Z" fill="black" />
												</svg>
											</span>
                                            <!--end::Svg Icon-->
										</span>
										<span class="menu-title">Penjualan</span>
										<span class="menu-arrow"></span>
									</span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('penjualan') ? 'active' : ''}}" href="{{route('penjualan')}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Penjualan</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('penjualan/trans') ? 'active' : ''}}" href="{{route('penjualan.trans')}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Penjualan Baru</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('penjualan/retur/baik') ? 'active' : ''}}" href="{{url('/').'/penjualan/retur/baik'}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Retur Baik</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('penjualan/retur/baik/trans') ? 'active' : ''}}" href="{{url('/').'/penjualan/retur/baik/trans'}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Retur Baik Baru</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('penjualan/retur/rusak') ? 'active' : ''}}" href="{{url('/').'/penjualan/retur/rusak'}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Retur Rusak</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('penjualan/retur/rusak/trans') ? 'active' : ''}}" href="{{url('/').'/penjualan/retur/rusak/trans'}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Retur Rusak Baru</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('pembelian*') ? 'here show' : ''}} menu-accordion">
									<span class="menu-link">
										<span class="menu-icon">
											<!--begin::Svg Icon | path: icons/duotune/general/gen002.svg-->
											<span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path opacity="0.3" d="M4.05424 15.1982C8.34524 7.76818 13.5782 3.26318 20.9282 2.01418C21.0729 1.98837 21.2216 1.99789 21.3618 2.04193C21.502 2.08597 21.6294 2.16323 21.7333 2.26712C21.8372 2.37101 21.9144 2.49846 21.9585 2.63863C22.0025 2.7788 22.012 2.92754 21.9862 3.07218C20.7372 10.4222 16.2322 15.6552 8.80224 19.9462L4.05424 15.1982ZM3.81924 17.3372L2.63324 20.4482C2.58427 20.5765 2.5735 20.7163 2.6022 20.8507C2.63091 20.9851 2.69788 21.1082 2.79503 21.2054C2.89218 21.3025 3.01536 21.3695 3.14972 21.3982C3.28408 21.4269 3.42387 21.4161 3.55224 21.3672L6.66524 20.1802L3.81924 17.3372ZM16.5002 5.99818C16.2036 5.99818 15.9136 6.08615 15.6669 6.25097C15.4202 6.41579 15.228 6.65006 15.1144 6.92415C15.0009 7.19824 14.9712 7.49984 15.0291 7.79081C15.0869 8.08178 15.2298 8.34906 15.4396 8.55884C15.6494 8.76862 15.9166 8.91148 16.2076 8.96935C16.4986 9.02723 16.8002 8.99753 17.0743 8.884C17.3484 8.77046 17.5826 8.5782 17.7474 8.33153C17.9123 8.08486 18.0002 7.79485 18.0002 7.49818C18.0002 7.10035 17.8422 6.71882 17.5609 6.43752C17.2796 6.15621 16.8981 5.99818 16.5002 5.99818Z" fill="black" />
													<path d="M4.05423 15.1982L2.24723 13.3912C2.15505 13.299 2.08547 13.1867 2.04395 13.0632C2.00243 12.9396 1.9901 12.8081 2.00793 12.679C2.02575 12.5498 2.07325 12.4266 2.14669 12.3189C2.22013 12.2112 2.31752 12.1219 2.43123 12.0582L9.15323 8.28918C7.17353 10.3717 5.4607 12.6926 4.05423 15.1982ZM8.80023 19.9442L10.6072 21.7512C10.6994 21.8434 10.8117 21.9129 10.9352 21.9545C11.0588 21.996 11.1903 22.0083 11.3195 21.9905C11.4486 21.9727 11.5718 21.9252 11.6795 21.8517C11.7872 21.7783 11.8765 21.6809 11.9402 21.5672L15.7092 14.8442C13.6269 16.8245 11.3061 18.5377 8.80023 19.9442ZM7.04023 18.1832L12.5832 12.6402C12.7381 12.4759 12.8228 12.2577 12.8195 12.032C12.8161 11.8063 12.725 11.5907 12.5653 11.4311C12.4057 11.2714 12.1901 11.1803 11.9644 11.1769C11.7387 11.1736 11.5205 11.2583 11.3562 11.4132L5.81323 16.9562L7.04023 18.1832Z" fill="black" />
												</svg>
											</span>
                                            <!--end::Svg Icon-->
										</span>
										<span class="menu-title">Pembelian</span>
										<span class="menu-arrow"></span>
									</span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('pembelian') ? 'active' : ''}}" href="{{route('pembelian')}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Pembelian</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('pembelian/trans') ? 'active' : ''}}" href="{{route('pembelian.trans')}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Pembelian Baru</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('pembelian/retur/baik') ? 'active' : ''}}" href="{{url('/').'/pembelian/retur/baik'}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Retur Baik</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('pembelian/retur/baik/trans') ? 'active' : ''}}" href="{{url('/').'/pembelian/retur/baik/trans'}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Retur Baik Baru</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('pembelian/retur/rusak') ? 'active' : ''}}" href="{{url('/').'/pembelian/retur/rusak'}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Retur Rusak</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{request()->is('pembelian/retur/rusak/trans') ? 'active' : ''}}" href="{{url('/').'/pembelian/retur/rusak/trans'}}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Retur Rusak Baru</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Stock</span>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock*') ? 'here show' : ''}} menu-accordion">
									<span class="menu-link">
										<span class="menu-icon">
											<!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm001.svg-->
											<span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="black" />
													<path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="black" />
													<path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="black" />
													<path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="black" />
												</svg>
											</span>
                                            <!--end::Svg Icon-->
										</span>
										<span class="menu-title">Stock</span>
										<span class="menu-arrow"></span>
									</span>
                    <div class="menu-sub menu-sub-accordion">
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/inventory*') ? 'here show' : ''}} menu-accordion mb-1">
											<span class="menu-link">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">Stock Report</span>
												<span class="menu-arrow"></span>
											</span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/inventory') ? 'active' : ''}}" href="{{route('inventory')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Stock Semua</span>
                                    </a>
                                </div>
                                @foreach($gudang as $row)
                                    @foreach($jenis as $item)
                                        <div class="menu-item">
                                            <a class="menu-link {{request()->is('stock/inventory/'.$item['jenis'].'/'.$row->id) ? 'active' : ''}}" href="{{url('/').'/stock/inventory/'.$item['jenis'].'/'.$row->id}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                                <span class="menu-title">Stock {{ucwords($row->nama)}} {{ucwords($item['jenis'])}}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/transaksi/masuk*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">Stock Masuk</span>
												<span class="menu-arrow"></span>
											</span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/masuk') ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/masuk'}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Stock Masuk</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/masuk/transaksi') ? 'active' : ''}}" href="{{route('stock.masuk.trans')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Stock Masuk Baru</span>
                                    </a>
                                </div>
                                    @foreach($kondisi as $item)
                                        <div class="menu-item">
                                            <a class="menu-link {{request()->is('stock/transaksi/masuk/'.$item['kondisi']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/masuk/'.$item['kondisi']}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                                <span class="menu-title">Stock Masuk {{ucwords($item['kondisi'])}}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                    @foreach($kondisi as $item)
                                    <div class="menu-item">
                                        <a class="menu-link {{request()->is('stock/transaksi/masuk/trans/'.$item['kondisi']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/masuk/trans/'.$item['kondisi']}}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                            <span class="menu-title">Stock Masuk {{ucwords($item['kondisi'])}} Baru</span>
                                        </a>
                                    </div>
                                    @endforeach
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/transaksi/keluar*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">Stock Keluar</span>
												<span class="menu-arrow"></span>
											</span>
                            <div class="menu-sub menu-sub-accordion">
                                    @foreach($kondisi as $item)
                                    <div class="menu-item">
                                        <a class="menu-link {{request()->is('stock/transaksi/keluar/'.$item['kondisi']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/keluar/'.$item['kondisi']}}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                            <span class="menu-title">Stock Keluar {{ucwords($item['kondisi'])}}</span>
                                        </a>
                                    </div>
                                    @endforeach
                                    @foreach($kondisi as $item)
                                    <div class="menu-item">
                                        <a class="menu-link {{request()->is('stock/transaksi/keluar/trans/'.$item['kondisi']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/keluar/trans/'.$item['kondisi']}}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                            <span class="menu-title">Stock Keluar {{ucwords($item['kondisi'])}} Baru</span>
                                        </a>
                                    </div>
                                    @endforeach
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/transaksi/opname*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">Stock Opname</span>
												<span class="menu-arrow"></span>
											</span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/opname') ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/opname'}}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                        <span class="menu-title">Stock Opname</span>
                                    </a>
                                </div>
                                    @foreach($jenis as $item)
                                    <div class="menu-item">
                                        <a class="menu-link {{request()->is('stock/transaksi/opname/'.$item['jenis']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/opname/'.$item['jenis']}}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                            <span class="menu-title">Stock Opname {{ucwords($item['jenis'])}}</span>
                                        </a>
                                    </div>
                                    @endforeach
                                    @foreach($jenis as $item)
                                    <div class="menu-item">
                                        <a class="menu-link {{request()->is('stock/transaksi/opname/trans/'.$item['jenis']) ? 'active' : ''}}" href="{{url('/').'/stock/transaksi/opname/trans/'.$item['jenis']}}">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                            <span class="menu-title">Stock Opname {{ucwords($item['jenis'])}} Baru</span>
                                        </a>
                                    </div>
                                    @endforeach
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/transaksi/mutasi*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">Stock Mutasi</span>
												<span class="menu-arrow"></span>
											</span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/baik_baik') ? 'active' : ''}}" href="{{route('mutasi.baik_baik')}}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                        <span class="menu-title">Mutasi Stock Baik</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/baik_baik/trans') ? 'active' : ''}}" href="{{route('mutasi.baik_baik.trans')}}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                        <span class="menu-title">Mutasi Stock Baik Baru</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/baik_rusak') ? 'active' : ''}}" href="{{route('mutasi.baik_rusak')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Mutasi Stock Baik - Rusak</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/baik_rusak/trans') ? 'active' : ''}}" href="{{route('mutasi.baik_rusak.trans')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Mutasi Stock Baik - Rusak Baru</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/rusak_rusak') ? 'active' : ''}}" href="{{route('mutasi.rusak_rusak')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Mutasi Stock Rusak</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/mutasi/rusak/rusak/trans') ? 'active' : ''}}" href="{{route('mutasi.rusak_rusak.trans')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Mutasi Stock Rusak Baru</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/stockakhir*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stock Akhir</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/stockakhir') ? 'active' : ''}}" href="{{route('stock.stockakhir')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Stock Akhir</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/stockakhir/transaksi*') ? 'active' : ''}}" href="{{route('stock.stockakhir.transaksi')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Stock Akhir Baru</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('stock/transaksi/internal*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Stock Internal</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('stock/transaksi/internal') ? 'active' : ''}}" href="{{route('stock.masuk.internal.index')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Stock Masuk Internal</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="menu-content pt-8 pb-0">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Keuangan</span>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/*') ? 'here show' : ''}} menu-accordion">
									<span class="menu-link">
										<span class="menu-icon fas fa-file-invoice">
											<!--begin::Svg Icon | path: icons/duotune/abstract/abs042.svg-->
											{{-- <span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path d="M18 21.6C16.6 20.4 9.1 20.3 6.3 21.2C5.7 21.4 5.1 21.2 4.7 20.8L2 18C4.2 15.8 10.8 15.1 15.8 15.8C16.2 18.3 17 20.5 18 21.6ZM18.8 2.8C18.4 2.4 17.8 2.20001 17.2 2.40001C14.4 3.30001 6.9 3.2 5.5 2C6.8 3.3 7.4 5.5 7.7 7.7C9 7.9 10.3 8 11.7 8C15.8 8 19.8 7.2 21.5 5.5L18.8 2.8Z" fill="black" />
													<path opacity="0.3" d="M21.2 17.3C21.4 17.9 21.2 18.5 20.8 18.9L18 21.6C15.8 19.4 15.1 12.8 15.8 7.8C18.3 7.4 20.4 6.70001 21.5 5.60001C20.4 7.00001 20.2 14.5 21.2 17.3ZM8 11.7C8 9 7.7 4.2 5.5 2L2.8 4.8C2.4 5.2 2.2 5.80001 2.4 6.40001C2.7 7.40001 3.00001 9.2 3.10001 11.7C3.10001 15.5 2.40001 17.6 2.10001 18C3.20001 16.9 5.3 16.2 7.8 15.8C8 14.2 8 12.7 8 11.7Z" fill="black" />
												</svg>
											</span> --}}<i class="fa-solid fa-cash-register"></i>
                                            <!--end::Svg Icon-->
										</span>
										<span class="menu-title">Master Keuangan</span>
										<span class="menu-arrow"></span>
									</span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/master/*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Master</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/master/akun') ? 'active' : ''}}" href="{{route('keuangan.master.akun')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Akun</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/master/akuntipe') ? 'active' : ''}}" href="{{route('keuangan.master.akuntipe')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Akun Tipe</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/master/akunkategori') ? 'active' : ''}}" href="{{route('keuangan.master.akunkategori')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Akun Kategori</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/persediaan/*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Persediaan</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/persediaan/opname') ? 'active' : ''}}" href="{{route('persediaan.opname')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Persediaan Opname</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/persediaan/opname/trans') ? 'active' : ''}}" href="{{route('persediaan.opname.t')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Persediaan Opname Baru</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/penjualan/*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Penjualan</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/penjualan') ? 'active' : ''}}" href="{{route('keuangan.master.akun')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Penerimaan Penjualan</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/penjualan/piutang') ? 'active' : ''}}" href="{{route('penjualan.piutang')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Piutang Penjualan</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/penjualan/piutanglama') ? 'active' : ''}}" href="{{route('penjualan.piutanglama')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Piutang Penjualan Lama</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/penjualan/piutangretur') ? 'active' : ''}}" href="{{route('penjualan.piutangretur')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Piutang Penjualan Retur</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/jurnal/*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Jurnal</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/jurnal/piutangpenjualan') ? 'active' : ''}}" href="{{route('keuangan.jurnal.piutangpenjualan')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Jurnal Piutang Penjualan</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/kasir/*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Kasir</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/kasir/penjualan') ? 'active' : ''}}" href="{{route('keuangan.kasir.penjualan')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Kasir Penerimaan Penjualan</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/kasir/penjualan/penerimaan') ? 'active' : ''}}" href="{{route('keuangan.kasir.penjualan.penerimaan')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Kasir Penerimaan Penjualan Baru</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/neraca/*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Neraca Saldo</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/neraca/saldo/awal') ? 'active' : ''}}" href="{{route('keuangan.neraca.saldoawal')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Neraca Saldo Awal</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/config/*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Konfigurasi Jurnal</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/config/jurnal') ? 'active' : ''}}" href="{{route('config.jurnal')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Jurnal</span>
                                    </a>
                                    <a class="menu-link {{request()->is('keuangan/config/hpp') ? 'active' : ''}}" href="{{route('config.hpp')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Hpp</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-kt-menu-trigger="click" class="menu-item {{request()->is('keuangan/tester/*') ? 'here show' : ''}} menu-accordion mb-1">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tester</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion">
                                <div class="menu-item">
                                    <a class="menu-link {{request()->is('keuangan/tester/index') ? 'active' : ''}}" href="{{route('keuangan.persediaan')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Persediaan Tester</span>
                                    </a>
                                    <a class="menu-link {{request()->is('keuangan/tester/transaksi') ? 'active' : ''}}" href="{{route('keuangan.persediaan.transaksi')}}">
														<span class="menu-bullet">
															<span class="bullet bullet-dot"></span>
														</span>
                                        <span class="menu-title">Persediaan Tester Baru</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
									<span class="menu-link">
										<span class="menu-icon">
											<!--begin::Svg Icon | path: icons/duotune/general/gen009.svg-->
											<span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path opacity="0.3" d="M21 22H14C13.4 22 13 21.6 13 21V3C13 2.4 13.4 2 14 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22Z" fill="black" />
													<path d="M10 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H10C10.6 2 11 2.4 11 3V21C11 21.6 10.6 22 10 22Z" fill="black" />
												</svg>
											</span>
                                            <!--end::Svg Icon-->
										</span>
										<span class="menu-title">Aside</span>
										<span class="menu-arrow"></span>
									</span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div class="menu-item">
                            <a class="menu-link" href="../../demo1/dist/layouts/aside/light.html">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Light Skin</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="../../demo1/dist/layouts/aside/font-icons.html">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Font Icons</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="../../demo1/dist/layouts/aside/minimized.html">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Minimized</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="../../demo1/dist/layouts/aside/only-header.html">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
                                <span class="menu-title">Only Header</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Menu-->
        </div>
        <!--end::Aside Menu-->
    </div>
    <!--end::Aside menu-->
    <!--begin::Footer-->

    <!--end::Footer-->
</div>
