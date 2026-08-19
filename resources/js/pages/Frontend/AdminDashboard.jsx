import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function AdminDashboard() {
    useEffect(() => {
        // Init scripts
    }, []);

    return (
        


<>
    {/*  Sidebar  */}
    <aside className="sidebar">
        <div className="sidebar-header">
            <div className="logo">Nova<span>Mart</span></div>
            <p>Admin Panel</p>
        </div>
        <nav className="sidebar-nav">
            <div className="nav-section">
                <a href="admin-dashboard.html" className="nav-link active"><i className="fas fa-home"></i><span>Dashboard</span></a>
            </div>
            <div className="nav-section">
                <div className="nav-section-title">E-Commerce</div>
                <a href="#" className="nav-link"><i className="fas fa-shopping-bag"></i><span>Orders</span><span className="badge">12</span></a>
                <a href="#" className="nav-link"><i className="fas fa-undo-alt"></i><span>Returns</span><span className="badge">3</span></a>
                <a href="#" className="nav-link"><i className="fas fa-wallet"></i><span>Payouts</span><span className="badge">5</span></a>
                <a href="#" className="nav-link"><i className="fas fa-chart-line"></i><span>Reports</span></a>
                <a href="#" className="nav-link"><i className="far fa-comment-dots"></i><span>Messages</span><span className="badge">8</span></a>
                <a href="#" className="nav-link"><i className="fas fa-bell"></i><span>Notifications</span><span className="badge">4</span></a>
                <a href="#" className="nav-link"><i className="fas fa-shield-alt"></i><span>Audit Logs</span></a>
                <a href="#" className="nav-link"><i className="fas fa-wave-square"></i><span>Observability</span></a>
                <a href="#" className="nav-link"><i className="fas fa-box"></i><span>Products</span></a>
                <a href="#" className="nav-link"><i className="fas fa-star-half-alt"></i><span>Reviews</span></a>
                <a href="#" className="nav-link"><i className="fas fa-folder"></i><span>Categories</span></a>
                <a href="#" className="nav-link"><i className="fas fa-images"></i><span>Banners</span></a>
            </div>
            <div className="nav-section">
                <div className="nav-section-title">Users</div>
                <a href="#" className="nav-link"><i className="fas fa-store"></i><span>Vendors</span><span className="badge">2</span></a>
                <a href="#" className="nav-link"><i className="fas fa-users"></i><span>Users</span></a>
            </div>
            <div className="nav-section">
                <div className="nav-section-title">Settings</div>
                <a href="#" className="nav-link"><i className="fas fa-truck"></i><span>Shipping</span></a>
                <a href="#" className="nav-link"><i className="fas fa-external-link-alt"></i><span>View Store</span></a>
                <a href="#" className="nav-link"><i className="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </nav>
    </aside>

    {/*  Main Content  */}
    <div className="main-wrapper">
        {/*  Header  */}
        <header className="admin-header">
            <div className="header-left-tools">
                <button type="button" className="button-show-hide" aria-label="Toggle sidebar"><i className="fas fa-bars"></i></button>
                <div className="search-box">
                    <i className="fas fa-search ex-style-11"></i>
                    <input type="text" placeholder="Search..." />
                </div>
            </div>
            <div className="header-actions">
                <div className="notification-dropdown">
                    <button type="button" className="header-btn" aria-label="Notifications">
                        <i className="fas fa-bell"></i>
                        <span className="badge">4</span>
                    </button>
                </div>
                <div className="user-menu">
                    <div className="info">
                        <div className="name">Admin User</div>
                        <div className="role">super-admin</div>
                    </div>
                    <div className="avatar">A</div>
                </div>
            </div>
        </header>

        {/*  Content  */}
        <main className="admin-content">
            <div className="page-header">
                <div>
                    <h1>Dashboard</h1>
                    <div className="breadcrumb">
                        <a href="#">Home</a>
                        <i className="fas fa-chevron-right ex-style-12"></i>
                        <span>Dashboard</span>
                    </div>
                </div>
                <div><span className="ex-style-13">Monday, July 14, 2025</span></div>
            </div>

            {/*  Stats  */}
            <div className="stats-grid">
                <div className="stat-card">
                    <div className="icon blue"><i className="fas fa-shopping-bag"></i></div>
                    <div className="value">1,234</div>
                    <div className="label">Total Orders</div>
                    <div className="change up"><i className="fas fa-arrow-up"></i> 48 today</div>
                </div>
                <div className="stat-card">
                    <div className="icon green"><i className="fas fa-dollar-sign"></i></div>
                    <div className="value">৳5,67,890</div>
                    <div className="label">Total Revenue</div>
                    <div className="change up"><i className="fas fa-arrow-up"></i> ৳12,340 today</div>
                </div>
                <div className="stat-card">
                    <div className="icon orange"><i className="fas fa-box"></i></div>
                    <div className="value">3,456</div>
                    <div className="label">Active Products</div>
                </div>
                <div className="stat-card">
                    <div className="icon purple"><i className="fas fa-store"></i></div>
                    <div className="value">89</div>
                    <div className="label">Active Vendors</div>
                    <div className="change ex-style-14"><i className="fas fa-clock"></i> 5 pending</div>
                </div>
            </div>

            <div className="ex-style-15">
                {/*  Recent Orders  */}
                <div className="card">
                    <div className="card-header">
                        <h3>Recent Orders</h3>
                        <a href="#" className="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div className="table-wrapper">
                        <table>
                            <thead>
                                <tr><th>Order ID</th><th>Customer</th><th>Vendor</th><th>Total</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#" className="ex-style-16">#ORD-1234</a></td>
                                    <td><a href="#" className="ex-style-17">Ahmed Rahman</a></td>
                                    <td>TechStore BD</td>
                                    <td className="ex-style-18">৳4,500</td>
                                    <td><span className="badge badge-warning">Pending</span></td>
                                    <td className="ex-style-13">2 hours ago</td>
                                </tr>
                                <tr>
                                    <td><a href="#" className="ex-style-16">#ORD-1233</a></td>
                                    <td><a href="#" className="ex-style-17">Sara Khan</a></td>
                                    <td>Fashion Hub</td>
                                    <td className="ex-style-18">৳2,800</td>
                                    <td><span className="badge badge-success">Delivered</span></td>
                                    <td className="ex-style-13">5 hours ago</td>
                                </tr>
                                <tr>
                                    <td><a href="#" className="ex-style-16">#ORD-1232</a></td>
                                    <td><a href="#" className="ex-style-17">Karim Uddin</a></td>
                                    <td>Home Appliances</td>
                                    <td className="ex-style-18">৳15,200</td>
                                    <td><span className="badge badge-info">Processing</span></td>
                                    <td className="ex-style-13">1 day ago</td>
                                </tr>
                                <tr>
                                    <td><a href="#" className="ex-style-16">#ORD-1231</a></td>
                                    <td><a href="#" className="ex-style-17">Nusrat Jahan</a></td>
                                    <td>Beauty Plus</td>
                                    <td className="ex-style-18">৳3,499</td>
                                    <td><span className="badge badge-danger">Cancelled</span></td>
                                    <td className="ex-style-13">1 day ago</td>
                                </tr>
                                <tr>
                                    <td><a href="#" className="ex-style-16">#ORD-1230</a></td>
                                    <td><a href="#" className="ex-style-17">Rafiq Ahmed</a></td>
                                    <td>Gadget World</td>
                                    <td className="ex-style-18">৳8,750</td>
                                    <td><span className="badge badge-success">Delivered</span></td>
                                    <td className="ex-style-13">2 days ago</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {/*  Quick Stats  */}
                <div>
                    <div className="card ex-style-19">
                        <div className="card-header"><h3>Order Status</h3></div>
                        <div className="card-body">
                            <div className="ex-style-20">
                                <div className="ex-style-4">
                                    <span className="ex-style-21"><span className="ex-style-22"></span>Pending</span>
                                    <span className="ex-style-23">48</span>
                                </div>
                                <div className="ex-style-4">
                                    <span className="ex-style-21"><span className="ex-style-24"></span>Delivered</span>
                                    <span className="ex-style-23">892</span>
                                </div>
                                <div className="ex-style-4">
                                    <span className="ex-style-21"><span className="ex-style-25"></span>Canceled</span>
                                    <span className="ex-style-23">23</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="card">
                        <div className="card-header"><h3>Top Products</h3></div>
                        <div className="card-body">
                            <div className="ex-style-26">
                                <img src="/frontend/images/products/dummy/dummy_98.webp" alt="" className="ex-style-27" />
                                <div className="ex-style-28">
                                    <p className="ex-style-29">Wireless Bluetooth Headphones</p>
                                    <p className="ex-style-30">312 sold</p>
                                </div>
                            </div>
                            <div className="ex-style-26">
                                <img src="/frontend/images/products/dummy/dummy_73.webp" alt="" className="ex-style-27" />
                                <div className="ex-style-28">
                                    <p className="ex-style-29">Smartwatch Pro Max</p>
                                    <p className="ex-style-30">245 sold</p>
                                </div>
                            </div>
                            <div className="ex-style-31">
                                <img src="/frontend/images/products/dummy/dummy_15.webp" alt="" className="ex-style-27" />
                                <div className="ex-style-28">
                                    <p className="ex-style-29">Premium Running Shoes</p>
                                    <p className="ex-style-30">189 sold</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</>


    );
}
