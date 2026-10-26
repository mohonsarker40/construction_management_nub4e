@php
    $roleId = (int) (auth()->user()->role_id ?? 0);
@endphp

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-building"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Construction Management</div>
    </a>

    <!-- Sidebar Search -->
    <div class="sidebar-search px-3 py-3">
        <div class="input-group">
            <input type="text" class="form-control sidebar-search-input" id="sidebarSearch" placeholder="Search menu..." autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text sidebar-search-icon"><i class="fas fa-search"></i></span>
            </div>
        </div>
    </div>
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- Role IDs: 1 Admin, 2 Project Manager, 3 Site Supervisor, 4 Designer, 5 Procurement, 6 Store Keeper, 7 Accountant, 8 Client. --}}
    @if($roleId === 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="false" aria-controls="collapseMaster"><i class="fas fa-fw fa-cogs"></i><span>Master Setup</span></a>
            <div id="collapseMaster" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.roles.index') }}"><i class="fas fa-circle"></i><span>Roles</span></a>
                <a class="collapse-item" href="{{ route('admin.users.index') }}"><i class="fas fa-circle"></i><span>Users</span></a>
                <a class="collapse-item" href="{{ route('admin.clients.index') }}"><i class="fas fa-circle"></i><span>Client Management</span></a>
                <a class="collapse-item" href="{{ route('admin.staffManagement.index') }}"><i class="fas fa-circle"></i><span>Staff Management</span></a>
                <a class="collapse-item" href="{{ route('admin.suppliers.index') }}"><i class="fas fa-circle"></i><span>Suppliers</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Materials</span></a>
            </div></div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProject" aria-expanded="false" aria-controls="collapseProject"><i class="fas fa-fw fa-project-diagram"></i><span>Project Management</span></a>
            <div id="collapseProject" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Projects &amp; Building</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Assignments</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Tasks &amp; Milestones</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Documents</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Status History</span></a>
            </div></div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory"><i class="fas fa-fw fa-warehouse"></i><span>Inventory / Procurement</span></a>
            <div id="collapseInventory" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Material Orders</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Site Inventory</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Transactions</span></a>
            </div></div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports"><i class="fas fa-fw fa-chart-bar"></i><span>Reports</span></a>
            <div id="collapseReports" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Reports</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Task Reports</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Inventory &amp; Order Reports</span></a>
            </div></div>
        </li>
    @elseif($roleId === 2)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProjectManager" aria-expanded="false" aria-controls="collapseProjectManager"><i class="fas fa-fw fa-project-diagram"></i><span>My Projects</span></a>
            <div id="collapseProjectManager" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Projects &amp; Team</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Tasks &amp; Milestones</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Documents</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Reports</span></a>
            </div></div>
        </li>
    @elseif($roleId === 3)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSiteWork" aria-expanded="false" aria-controls="collapseSiteWork"><i class="fas fa-fw fa-hard-hat"></i><span>Site Work</span></a>
            <div id="collapseSiteWork" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Assigned Projects</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Tasks &amp; Milestones</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Documents</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Site Inventory</span></a>
            </div></div>
        </li>
    @elseif($roleId === 4)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDesignWork" aria-expanded="false" aria-controls="collapseDesignWork"><i class="fas fa-fw fa-drafting-compass"></i><span>Design Work</span></a>
            <div id="collapseDesignWork" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Assigned Projects</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Documents</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Milestones</span></a>
            </div></div>
        </li>
    @elseif($roleId === 5)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProcurement" aria-expanded="false" aria-controls="collapseProcurement"><i class="fas fa-fw fa-truck"></i><span>Procurement</span></a>
            <div id="collapseProcurement" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Suppliers &amp; Materials</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Material Orders</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Order Reports</span></a>
            </div></div>
        </li>
    @elseif($roleId === 6)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStore" aria-expanded="false" aria-controls="collapseStore"><i class="fas fa-fw fa-warehouse"></i><span>Store &amp; Inventory</span></a>
            <div id="collapseStore" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Materials</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Site Inventory</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Inventory Update</span></a>
            </div></div>
        </li>
    @elseif($roleId === 7)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAccounts" aria-expanded="false" aria-controls="collapseAccounts"><i class="fas fa-fw fa-file-invoice-dollar"></i><span>Accounts &amp; Reports</span></a>
            <div id="collapseAccounts" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Projects</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Material Orders</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Order Reports</span></a>
            </div></div>
        </li>
    @elseif($roleId === 8)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClientProjects" aria-expanded="false" aria-controls="collapseClientProjects"><i class="fas fa-fw fa-building"></i><span>My Projects</span></a>
            <div id="collapseClientProjects" class="collapse" data-parent="#accordionSidebar"><div class="collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Progress</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Milestones</span></a>
                <a class="collapse-item" href="#"><i class="fas fa-circle"></i><span>Project Documents</span></a>
            </div></div>
        </li>
    @endif

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

                const submenuLinks = submenu ? Array.from(submenu.querySelectorAll('.collapse-item')) : [];
                const parentMatches = parentLink.textContent.toLowerCase().includes(keyword);
                const submenuMatches = submenuLinks.some(link => link.textContent.toLowerCase().includes(keyword));
                item.style.display = !keyword || parentMatches || submenuMatches ? '' : 'none';

                submenuLinks.forEach(link => {
                    link.style.display = !keyword || parentMatches || link.textContent.toLowerCase().includes(keyword) ? '' : 'none';
                });

                if (submenu && keyword) {
                    const shouldOpen = parentMatches || submenuMatches;
                    if (window.jQuery) jQuery(submenu).collapse(shouldOpen ? 'show' : 'hide');
                    else submenu.classList.toggle('show', shouldOpen);
                    parentLink.classList.toggle('collapsed', !shouldOpen);
                    parentLink.setAttribute('aria-expanded', String(shouldOpen));
                }
            });
        });
    });
</script>
