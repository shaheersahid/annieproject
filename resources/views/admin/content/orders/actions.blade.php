<div class="d-flex gap-2">
    @can('view orders')
      @php
          $viewUrl = request()->routeIs('admin.pos-orders')
              ? route('admin.pos-orders.show', $order)
              : route('admin.website-orders.show', $order);
      @endphp
      <x-admin.action-button type="view" :url="$viewUrl" />
    @endcan
    
    @can('delete orders')
      <x-admin.action-button type="delete" :url="$order->adminRoute('destroy')" />
    @endcan
</div>
