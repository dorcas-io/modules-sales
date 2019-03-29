<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 ml-auto">
                &nbsp;
            </div>
            <div class="col-lg order-lg-first">
                <ul class="nav nav-tabs border-0 flex-column flex-lg-row">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link" v-bind:class="{'active': selectedMenu === 'home'}">
                            <i class="fe fe-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('access-grants') }}" class="nav-link" v-bind:class="{'active': selectedMenu === 'access-grants'}"><i class="fe fe-unlock"></i> Access Grants</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="javascript:void(0)" class="nav-link" v-bind:class="{'active': selectedMenu === 'settings'}" data-toggle="dropdown">
                            <i class="fe fe-settings"></i> Settings
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            <a href="{{ route('settings.billing') }}" class="dropdown-item ">Subscription &amp; Billing</a>
                            <a href="{{ route('settings.business') }}" class="dropdown-item ">Business Profile Settings</a>
                            <a href="{{ route('settings.personal') }}" class="dropdown-item ">Account &amp; Profile</a>
                            <a href="{{ route('settings.security') }}" class="dropdown-item ">Security Settings</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
