{{-- 
	User Information
 --}}

<div class="menu-group">
	<div class="menu-user menu-link">
		<span>
			Hello!
		</span>
		<span>
			{{Auth::user()->name}}
		</span>
	</div>
	<div class="menu-user-icon-group">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin/edit-profile')}}" class="menu-user-icon">
			{!!HTML::image('img/admin/edit_profile.png', 'Edit Profile', ['class'=>'menu-user-img'])!!}
			<span>
				Edit Profile
			</span>
		</a>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/logout')}}" class="menu-user-icon logout">
			{!!HTML::image('img/admin/logout.png', 'Sign Out', ['class'=>'menu-user-img'])!!}
			<span>
				Sign Out
			</span>
		</a>
	</div>
</div>


{{-- 
	Navigation goes here
 --}}

<div class="menu-group">
	<a class="menu-title" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}" style="margin-bottom: 0px;">
		Dashboard
	</a>
</div>

<div class="menu-group">
	<div class="menu-title">
		Master
	</div>
	<div class="menu-link menu-switch">
		<span class="menu-link-hov">
			Location
		</span>

		<div class="menu-sub-menu-container">
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/gudang')}}" class="menu-sub-menu-link">
					Gudang
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/gudang/create')}}" class="menu-add"></a>
			</div>
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak')}}" class="menu-sub-menu-link">
					Rak
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/create')}}" class="menu-add"></a>
			</div>
		</div>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/kendaraan')}}" class="menu-link-hov">
			Kendaraan
		</a>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/kendaraan/create')}}" class="menu-add"></a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}" class="menu-link-hov">
			Product
		</a>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/create')}}" class="menu-add"></a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/adjustment')}}" class="menu-link-hov">
			Stock Adjustment
		</a>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/adjustment/create')}}" class="menu-add"></a>
	</div>
	<div class="menu-link menu-switch">
		<span class="menu-link-hov">
			Account
		</span>

		<div class="menu-sub-menu-container">
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/account')}}" class="menu-sub-menu-link">
					Account
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/account/create')}}" class="menu-add"></a>
			</div>
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')}}" class="menu-sub-menu-link">
					Other Expend / Revenue
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue/create')}}" class="menu-add"></a>
			</div>
		</div>
	</div>
</div>

<div class="menu-group">
	<div class="menu-title">
		Transaction
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier')}}" class="menu-link-hov">
			Supplier
		</a>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/create')}}" class="menu-add"></a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer')}}" class="menu-link-hov">
			Customer
		</a>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer/create')}}" class="menu-add"></a>
	</div>
	<div class="menu-link menu-switch">
		<span class="menu-link-hov">
			Order Management
		</span>

		<div class="menu-sub-menu-container">
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po')}}" class="menu-sub-menu-link">
					Purchase Order
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po/create')}}" class="menu-add"></a>
			</div>
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri')}}" class="menu-sub-menu-link">
					Recieve Item
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/create')}}" class="menu-add"></a>
			</div>
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/return')}}" class="menu-sub-menu-link">
					Return
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/create')}}" class="menu-add"></a>
			</div>
		</div>
	</div>
	<div class="menu-link menu-switch">
		<span class="menu-link-hov">
			Transaction Management
		</span>

		<div class="menu-sub-menu-container">
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction')}}" class="menu-sub-menu-link">
					Transaction
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/create')}}" class="menu-add"></a>
			</div>
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn')}}" class="menu-sub-menu-link">
					Return
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn/create')}}" class="menu-add"></a>
			</div>
		</div>
	</div>
	<div class="menu-link menu-switch">
		<span class="menu-link-hov">
			Debt
		</span>

		<div class="menu-sub-menu-container">
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/uninvoiced-debt')}}" class="menu-sub-menu-link">
					Uninvoiced Debt
				</a>
			</div>
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice')}}" class="menu-sub-menu-link">
					Invoice
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/create')}}" class="menu-add"></a>
			</div>
		</div>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/accounts-recievable')}}" class="menu-link-hov">
			Accounts Recievable
		</a>
	</div>
	<div class="menu-link menu-switch">
		<span class="menu-link-hov">
			Payment
		</span>

		<div class="menu-sub-menu-container">
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/payment')}}" class="menu-sub-menu-link">
					Order Payment
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/payment/create')}}" class="menu-add"></a>
			</div>
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment')}}" class="menu-sub-menu-link">
					Transaction Payment
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment/create')}}" class="menu-add"></a>
			</div>
		</div>
	</div>
</div>

<div class="menu-group">
	<div class="menu-title">
		Report
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock-card')}}" class="menu-link-hov">
			Stock Card
		</a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue/report')}}" class="menu-link-hov">
			Other Expend / Revenue Report
		</a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/income-statement/report')}}" class="menu-link-hov">
			Income Statement
		</a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/report')}}" class="menu-link-hov">
			Transaction Report
		</a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/accounts-recievable/report')}}" class="menu-link-hov">
			Accounts Recievable Report
		</a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/report')}}" class="menu-link-hov">
			Invoice Debt Report
		</a>
	</div>
</div>

<div class="menu-group">
	<div class="menu-title">
		Setting
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/bank')}}" class="menu-link-hov">
			Bank
		</a>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/bank/create')}}" class="menu-add"></a>
	</div>
	<div class="menu-link menu-switch">
		<span class="menu-link-hov">
			Admin
		</span>

		<div class="menu-sub-menu-container">
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup')}}" class="menu-sub-menu-link">
					Admin Group
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup/create')}}" class="menu-add"></a>
			</div>
			<div class="menu-sub-menu">
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin')}}" class="menu-sub-menu-link">
					Admin
				</a>
				<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin/create')}}" class="menu-add"></a>
			</div>
		</div>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/setting/edit')}}" class="menu-link-hov">
			Setting
		</a>
	</div>
	<div class="menu-link">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/logout')}}" class="menu-link-hov logout">
			Sign Out
		</a>
	</div>
</div>

<div class="menu-group">
	<div class="nav-powered-group menu-link">
		<span>
			Powered by
		</span>
		<a href="http://www.creids.net" class="nav-powered" title="CREIDS" target="_blank">
			{!!HTML::image('img/admin/creids_logo.png', 'CREIDS')!!}
		</a>
	</div>
</div>