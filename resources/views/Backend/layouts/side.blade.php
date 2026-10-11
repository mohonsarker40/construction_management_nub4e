<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-building"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Construction Management</div>
    </a>
    <!-- Sidebar Search -->
    <div class="sidebar-search px-3 py-3">
        <div class="input-group">
            <input type="text"
                   class="form-control sidebar-search-input"
                   id="sidebarSearch"
                   placeholder="Search menu..."
                   autocomplete="off">

            <div class="input-group-append">
            <span class="input-group-text sidebar-search-icon">
                <i class="fas fa-search"></i>
            </span>
            </div>
        </div>
    </div>
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Master Setup -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="false" aria-controls="collapseMaster">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Master Setup</span>
        </a>

        <div id="collapseMaster" class="collapse"
             data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>User &amp; Role Management</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Client Management</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Staff Management</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Suppliers</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Materials</span>
                </a>

            </div>
        </div>
    </li>

    <!-- Project Management -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProject" aria-expanded="false" aria-controls="collapseProject">
            <i class="fas fa-fw fa-project-diagram"></i>
            <span>Project Management</span>
        </a>

        <div id="collapseProject" class="collapse" data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Project &amp; Building</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Project Assignment</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Task Management</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Milestone Management</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Project Documents</span>
                </a>

                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Project Status History</span>
                </a>

            </div>
        </div>
    </li>

    <!-- Inventory & Procurement -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory">
            <i class="fas fa-fw fa-warehouse"></i>
            <span>Inventory / Procurement</span>
        </a>

        <div id="collapseInventory" class="collapse"
             data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Material Orders</span>
                </a>
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Order Items</span>
                </a>
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Site Inventory</span>
                </a>

            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransaction" aria-expanded="false" aria-controls="collapseTransaction">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Transaction</span>
        </a>
        <div id="collapseTransaction" class="collapse" data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Material Order Entry</span>
                </a>
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Inventory Update</span>
                </a>
            </div>
        </div>
    </li>

    <!-- Reports -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        <div id="collapseReports" class="collapse" data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Project Report</span>
                </a>
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Task Report</span>
                </a>
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Milestone Report</span>
                </a>
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Inventory Report</span>
                </a>
                <a class="collapse-item" href="#">
                    <i class="fas fa-circle"></i>
                    <span>Material Order Report</span>
                </a>
            </div>
        </div>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('sidebarSearch');
        const sidebar = document.getElementById('accordionSidebar');

        if (!searchInput || !sidebar) return;

        const menuItems = sidebar.querySelectorAll(':scope > li.nav-item');

        searchInput.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();

            menuItems.forEach(function (item) {
                const parentLink = item.querySelector(':scope > a.nav-link');
                const submenu = item.querySelector(':scope > div.collapse');

                if (!parentLink) return;

                const parentText = parentLink.textContent.toLowerCase();
                const submenuLinks = submenu
                    ? Array.from(submenu.querySelectorAll('.collapse-item'))
                    : [];

                const matchingSubmenus = submenuLinks.filter(function (link) {
                    return link.textContent.toLowerCase().includes(keyword);
                });

                const parentMatches = parentText.includes(keyword);
                const submenuMatches = matchingSubmenus.length > 0;

                if (!keyword) {
                    item.style.display = '';
                    submenuLinks.forEach(link => link.style.display = '');
                    return;
                }

                item.style.display = (parentMatches || submenuMatches) ? '' : 'none';

                if (submenu) {
                    submenuLinks.forEach(function (link) {
                        link.style.display =
                            (parentMatches || link.textContent.toLowerCase().includes(keyword))
                                ? ''
                                : 'none';
                    });

                    if (parentMatches || submenuMatches) {
                        if (window.jQuery) {
                            jQuery(submenu).collapse('show');
                        } else {
                            submenu.classList.add('show');
                        }
                        parentLink.classList.remove('collapsed');
                        parentLink.setAttribute('aria-expanded', 'true');
                    } else {
                        if (window.jQuery) {
                            jQuery(submenu).collapse('hide');
                        } else {
                            submenu.classList.remove('show');
                        }
                        parentLink.classList.add('collapsed');
                        parentLink.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });
    });
</script>
