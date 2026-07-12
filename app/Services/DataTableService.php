<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductTag;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Seller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\AbandonedCart;
use App\Models\Transaction;
use App\Models\User;
use App\Models\ReturnModel;
use App\Models\ProductReview;
use App\Models\ContactSubmission;
use Spatie\Permission\Models\Role;
use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\DataTableServiceInterface;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class DataTableService implements DataTableServiceInterface
{
    public function categoryProductsTable(Builder $query)
    {
        return DataTables::eloquent($query->with(['primaryImage']))
            ->addColumn('image', function ($product) {
                $url = $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png');
                return '<img src="' . $url . '" alt="' . e($product->name) . '" class="avatar-sm rounded">';
            })
            ->addColumn('details', function ($product) {
                return '<div><strong>' . e($product->name) . '</strong><br><small class="text-muted">' . e($product->sku ?? '-') . '</small></div>';
            })
            ->addColumn('price', function ($product) {
                return format_price($product->price);
            })
            ->rawColumns(['image', 'details'])
            ->make(true);
    }

    /**
     * Get query for products listing. Eager load relationship categories and brand.
     */
    public function getProductsQuery(): Builder
    {
        return Product::query()->with(['brand', 'categories']);
    }

    /**
     * Get products datatable
     */
    public function getProductsDataTable()
    {
        $query = $this->getProductsQuery()->with(['primaryImage', 'seller']);
        
        if (request('stock_status') === 'in_stock') {
            $query->where('stock', '>', 0);
        } elseif (request('stock_status') === 'low_stock') {
            $query->whereBetween('stock', [1, 10]);
        } elseif (request('stock_status') === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        }

        return DataTables::eloquent($query)
            ->addColumn('product_summary', function ($product) {
                $url = $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png');
                $variantBadge = $product->has_variants
                    ? '<span class="badge bg-info-subtle text-info ms-2">Variants</span>'
                    : '<span class="badge bg-secondary-subtle text-secondary ms-2">Single</span>';

                return '<div class="d-flex align-items-center gap-2">
                            <img src="' . $url . '" alt="' . e($product->name) . '" class="avatar-sm rounded">
                            <div>
                                <div class="fw-semibold">' . e($product->name) . $variantBadge . '</div>
                                <small class="text-muted">' . e($product->sku ?? '-') . '</small>
                            </div>
                        </div>';
            })
            ->addColumn('category', function ($product) {
                return $product->categories->pluck('name')->implode(', ');
            })
            ->addColumn('seller', function ($product) {
                return $product->seller?->store_name ?? '-';
            })
            ->addColumn('price_formatted', function ($product) {
                return format_price($product->base_price);
            })
            ->addColumn('status', function ($product) {
                $checked = $product->is_active ? 'checked' : '';

                return '<div class="form-check form-switch form-switch-sm d-flex justify-content-center">
                            <input class="form-check-input toggle-product-status" type="checkbox"
                                data-id="'.$product->id.'"
                                '.$checked.'>
                        </div>';
            })
            ->addColumn('action', function ($product) {
                return view('admin.content.product-management.products.actions', compact('product'))->render();
            })
            ->rawColumns(['product_summary', 'status', 'action'])
            ->make(true);
    }

    public function getDraftProductsDataTable()
    {
        $query = $this->getProductsQuery()->draft()->with('primaryImage');
        
        return DataTables::eloquent($query)
            ->addColumn('image', function ($product) {
                $url = $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png');
                return '<img src="' . $url . '" alt="' . $product->name . '" class="avatar-sm rounded">';
            })
            ->addColumn('categories', function ($product) {
                return $product->categories->pluck('name')->implode(', ');
            })
            ->addColumn('brand', function ($product) {
                return $product->brand ? $product->brand->name : '-';
            })
            ->addColumn('price_formatted', function ($product) {
                return format_price($product->price);
            })
            ->addColumn('action', function ($product) {
                return view('admin.content.product-management.products.actions', compact('product'))->render();
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }

    public function getStockProductsDataTable()
    {
        $query = $this->getProductsQuery()->with('primaryImage');
        
        return DataTables::eloquent($query)
            ->addColumn('product_summary', function ($product) {
                $url = $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png');
                return '<div class="d-flex align-items-center gap-2">
                            <img src="' . $url . '" alt="' . e($product->name) . '" class="avatar-sm rounded">
                            <div>
                                <div class="fw-semibold">' . e($product->name) . '</div>
                                <small class="text-muted">' . e($product->sku ?? '-') . '</small>
                            </div>
                        </div>';
            })
            ->addColumn('brand', function ($product) {
                return $product->brand ? $product->brand->name : '-';
            })
            ->addColumn('stock_badge', function ($product) {
                if($product->stock == 0) {
                    return '<span class="badge bg-danger">0</span>';
                } elseif($product->stock <= 10) {
                    return '<span class="badge bg-warning">' . $product->stock . '</span>';
                } else {
                    return '<span class="badge bg-success">' . $product->stock . '</span>';
                }
            })
            ->addColumn('status_badge', function ($product) {
                if($product->status == 'published') {
                    return '<span class="badge bg-success">Published</span>';
                } elseif($product->status == 'draft') {
                    return '<span class="badge bg-secondary">Draft</span>';
                } else {
                    return '<span class="badge bg-danger">Archived</span>';
                }
            })
            ->addColumn('action', function ($product) {
                return view('admin.content.product-management.products.stock-actions', compact('product'))->render();
            })
            ->rawColumns(['product_summary', 'stock_badge', 'status_badge', 'action'])
            ->make(true);
    }

    public function getManageInventoryDataTable()
    {
        $query = Product::query()->with(['categories', 'primaryImage']);

        return DataTables::eloquent($query)
            ->addColumn('product_summary', function ($product) {
                $url = $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png');
                return '<div class="d-flex align-items-center gap-2">
                            <img src="' . $url . '" alt="' . e($product->name) . '" class="avatar-sm rounded">
                            <div>
                                <div class="fw-semibold">' . e($product->name) . '</div>
                                <small class="text-muted">' . e($product->sku ?? '-') . '</small>
                            </div>
                        </div>';
            })
            ->addColumn('category', function ($product) {
                return $product->categories->pluck('name')->implode(', ') ?: '-';
            })
            ->addColumn('stock_badge', function ($product) {
                if ($product->stock == 0) {
                    return '<span class="badge bg-danger">0</span>';
                }

                if ($product->stock <= 10) {
                    return '<span class="badge bg-warning">'.$product->stock.'</span>';
                }

                return '<span class="badge bg-success">'.$product->stock.'</span>';
            })
            ->addColumn('sold_out_badge', function ($product) {
                return '<span class="badge bg-secondary">'.$product->sold_out.'</span>';
            })
            ->addColumn('action', function ($product) {
                return '<a href="'.route('admin.products.stocks.order-requests.show', $product).'" class="btn btn-sm btn-outline-success" title="Add Stock"><i class="fas fa-plus-circle me-1"></i> Add Stock</a>';
            })
            ->rawColumns(['product_summary', 'stock_badge', 'sold_out_badge', 'action'])
            ->make(true);
    }

    public function getProductReviewsDataTable()
    {
        $query = ProductReview::with(['product', 'reviewer']);
        
        return DataTables::eloquent($query)
            ->addColumn('product', function ($review) {
                return $review->product ? $review->product->name : '-';
            })
            ->addColumn('reviewer', function ($review) {
                return $review->reviewer ? $review->reviewer->name : '-';
            })
            ->addColumn('rating_stars', function ($review) {
                $stars = '';
                for($i = 1; $i <= 5; $i++) {
                    if($i <= $review->rating) {
                        $stars .= '<i class="mdi mdi-star text-warning"></i>';
                    } else {
                        $stars .= '<i class="mdi mdi-star text-muted"></i>';
                    }
                }
                return $stars;
            })
            ->addColumn('status_badge', function ($review) {
                if($review->status == 'approved') {
                    return '<span class="badge bg-success">Approved</span>';
                } elseif($review->status == 'rejected') {
                    return '<span class="badge bg-danger">Rejected</span>';
                } else {
                    return '<span class="badge bg-warning">Pending</span>';
                }
            })
            ->editColumn('created_at', function ($review) {
                return $review->created_at->format('d M Y');
            })
            ->addColumn('action', function ($review) {
                return view('admin.content.product-management.reviews.actions', compact('review'))->render();
            })
            ->rawColumns(['rating_stars', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Get categories datatable
     */
    public function getCategoriesDataTable()
    {
        $query = Category::with('parent')->ordered();

        return DataTables::eloquent($query)
            ->addColumn('parentCategory', function ($category) {
                return $category->parent ? $category->parent->name : '-';
            })
            ->addColumn('totalProducts', function ($category) {
                return $category->products()->count();
            })
            // ->addColumn('homepage', function ($category) {
            //     $checked = $category->show_on_home ? 'checked' : '';
            //     $disabled = auth()->user()->can('admin.categories.update') ? '' : 'disabled';
            //     return '<div class="form-check form-switch form-switch-sm d-flex justify-content-center">
            //                 <input class="form-check-input toggle-status" type="checkbox"
            //                     data-id="' . $category->id . '"
            //                     data-type="homepage"
            //                     ' . $checked . ' ' . $disabled . '>
            //             </div>';
            // })
            ->addColumn('status', function ($category) {
                $checked = $category->is_active ? 'checked' : '';
                $disabled = auth()->user()->can('admin.categories.update') ? '' : 'disabled';

                return '<div class="form-check form-switch form-switch-sm d-flex justify-content-center">
                            <input class="form-check-input toggle-status" type="checkbox" 
                                data-id="'.$category->id.'" 
                                data-type="status" 
                                '.$checked.' '.$disabled.'>
                        </div>';
            })
            ->addColumn('action', function ($category) {
                return view('admin.content.product-management.categories.actions', compact('category'))->render();
            })
            ->rawColumns(['homepage', 'status', 'action'])
            ->make(true);
    }

    /**
     * Get contact submissions datatable
     */
    public function getContactSubmissionsDataTable()
    {
        return DataTables::eloquent(ContactSubmission::latest())
            ->addColumn('status_badge', function ($contact) {
                $badges = [
                    'new' => 'primary',
                    'read' => 'info',
                    'replied' => 'success',
                    'archived' => 'secondary',
                ];
                $class = $badges[$contact->status] ?? 'secondary';

                return "<span class='badge badge-{$class}'>".ucfirst($contact->status).'</span>';
            })
            ->editColumn('created_at', function ($contact) {
                return $contact->created_at->format('d M Y, H:i');
            })
            ->addColumn('action', function ($contact) {
                return view('admin.content.contact.actions', compact('contact'));
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Get brands datatable
     */
    public function getBrandsDataTable()
    {
        $query = Brand::query();

        return DataTables::eloquent($query)
            ->addColumn('status', function ($brand) {
                $checked = $brand->is_active ? 'checked' : '';

                return '<div class="form-check form-switch form-switch-sm d-flex justify-content-center">
                            <input class="form-check-input toggle-status" type="checkbox" 
                                data-id="'.$brand->id.'" 
                                data-type="status" 
                                '.$checked.'>
                        </div>';
            })
            ->addColumn('action', function ($brand) {
                return view('admin.content.product-management.brands.actions', compact('brand'))->render();
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Get product attributes datatable
     */
    public function getProductAttributesDataTable()
    {
        $query = ProductAttribute::query()->with('categories');

        return DataTables::eloquent($query)
            ->addColumn('input_type_label', function ($attribute) {
                return ProductAttribute::INPUT_TYPES[$attribute->input_type] ?? str($attribute->input_type)->headline();
            })
            ->addColumn('categories_list', function ($attribute) {
                return $attribute->categories->pluck('name')->implode(', ') ?: '-';
            })
            ->addColumn('status', function ($attribute) {
                $checked = $attribute->is_active ? 'checked' : '';

                return '<div class="form-check form-switch form-switch-sm d-flex justify-content-center">
                            <input class="form-check-input toggle-status" type="checkbox"
                                data-id="'.$attribute->id.'"
                                '.$checked.'>
                        </div>';
            })
            ->addColumn('action', function ($attribute) {
                return view('admin.content.product-management.product-attributes.actions', compact('attribute'))->render();
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Get product tags datatable
     */
    public function getProductTagsDataTable()
    {
        $query = ProductTag::query();

        return DataTables::eloquent($query)
            ->addColumn('type_label', function ($tag) {
                return ProductTag::TYPES[$tag->type] ?? str($tag->type)->headline();
            })
            ->addColumn('option_label', function ($tag) {
                return $tag->option ? (ProductTag::OPTIONS[$tag->option] ?? str($tag->option)->headline()) : '-';
            })
            ->addColumn('products_count', function ($tag) {
                return $tag->products()->count();
            })
            ->addColumn('status', function ($tag) {
                $checked = $tag->is_active ? 'checked' : '';

                return '<div class="form-check form-switch form-switch-sm d-flex justify-content-center">
                            <input class="form-check-input toggle-status" type="checkbox"
                                data-id="'.$tag->id.'"
                                '.$checked.'>
                        </div>';
            })
            ->addColumn('action', function ($tag) {
                return view('admin.content.product-management.tags.actions', compact('tag'))->render();
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Get sellers datatable
     */
    public function getSellersDataTable()
    {
        $query = Seller::query();

        return DataTables::eloquent($query)
            ->addColumn('store', function ($seller) {
                $logo = $seller->store_logo
                    ? '<img src="'.asset('storage/'.$seller->store_logo).'" alt="'.e($seller->store_name).'" class="avatar-sm rounded me-2">'
                    : '';

                return '<div class="d-flex align-items-center">'.$logo.'<div><strong>'.e($seller->store_name).'</strong><br><small class="text-muted">@'.e($seller->username).'</small></div></div>';
            })
            ->addColumn('contact', function ($seller) {
                return '<div>'.e($seller->owner_name).'<br><small class="text-muted">'.e($seller->email).'</small></div>';
            })
            ->addColumn('status', function ($seller) {
                $checked = $seller->is_active ? 'checked' : '';

                return '<div class="form-check form-switch form-switch-sm d-flex justify-content-center">
                            <input class="form-check-input toggle-status" type="checkbox"
                                data-id="'.$seller->id.'"
                                '.$checked.'>
                        </div>';
            })
            ->addColumn('action', function ($seller) {
                return view('admin.content.sellers.actions', compact('seller'))->render();
            })
            ->rawColumns(['store', 'contact', 'status', 'action'])
            ->make(true);
    }


    /**
     * Get query for orders listing.
     */
    public function getOrdersQuery(): Builder
    {
        return Order::query()->with('customer');
    }

    public function getOrderManagementDataTable()
    {
        $query = Order::query()->with(['customer', 'seller']);

        return DataTables::eloquent($query)
            ->addColumn('order_id', fn ($order) => '#'.$order->id)
            ->addColumn('customer_name', fn ($order) => $order->customer?->name ?? '-')
            ->addColumn('seller_name', fn ($order) => $order->seller?->store_name ?? '-')
            ->addColumn('status_badge', function ($order) {
                $classes = [
                    'pending' => 'warning',
                    'processing' => 'primary',
                    'shipped' => 'info',
                    'delivered' => 'success',
                    'cancelled' => 'danger',
                    'returned' => 'secondary',
                    'failed' => 'danger',
                ];

                $class = $classes[$order->status] ?? 'secondary';

                return '<span class="badge bg-'.$class.'">'.e(ucfirst($order->status)).'</span>';
            })
            ->addColumn('grand_total_formatted', fn ($order) => format_price($order->grand_total))
            ->addColumn('created_at_formatted', fn ($order) => $order->created_at?->format('d M Y'))
            ->addColumn('action', function ($order) {
                return '<a href="'.route('admin.website-orders.show', $order).'" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function getReturnRefundsDataTable()
    {
        $query = OrderReturn::query()->with('order');

        return DataTables::eloquent($query)
            ->addColumn('return_id', fn ($return) => '#'.$return->id)
            ->addColumn('order_number', fn ($return) => $return->order?->order_number ?? '-')
            ->addColumn('status_badge', function ($return) {
                $classes = [
                    'requested' => 'warning',
                    'approved' => 'primary',
                    'rejected' => 'danger',
                    'refunded' => 'success',
                ];

                $class = $classes[$return->status] ?? 'secondary';

                return '<span class="badge bg-'.$class.'">'.e(ucfirst($return->status)).'</span>';
            })
            ->addColumn('refund_amount_formatted', fn ($return) => format_price($return->refund_amount))
            ->addColumn('requested_at_formatted', fn ($return) => $return->requested_at?->format('d M Y') ?? '-')
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    public function getAbandonedCartsDataTable()
    {
        $query = AbandonedCart::query()->with('user');

        return DataTables::eloquent($query)
            ->addColumn('cart_id', fn ($cart) => '#'.$cart->id)
            ->addColumn('user_name', fn ($cart) => $cart->user?->name ?? 'Guest')
            ->addColumn('cart_total_formatted', fn ($cart) => format_price($cart->cart_total))
            ->addColumn('abandoned_at_formatted', fn ($cart) => $cart->abandoned_at?->format('d M Y H:i') ?? '-')
            ->addColumn('recovered_at_formatted', fn ($cart) => $cart->recovered_at?->format('d M Y H:i') ?? '-')
            ->make(true);
    }

    public function getTransactionsDataTable()
    {
        $query = Transaction::query()->with('order');

        return DataTables::eloquent($query)
            ->addColumn('transaction_id', fn ($transaction) => '#'.$transaction->id)
            ->addColumn('order_number', fn ($transaction) => $transaction->order?->order_number ?? '-')
            ->addColumn('status_badge', function ($transaction) {
                $classes = [
                    'pending' => 'warning',
                    'paid' => 'success',
                    'failed' => 'danger',
                    'refunded' => 'secondary',
                ];

                $class = $classes[$transaction->status] ?? 'secondary';

                return '<span class="badge bg-'.$class.'">'.e(ucfirst($transaction->status)).'</span>';
            })
            ->addColumn('amount_formatted', fn ($transaction) => format_price($transaction->amount))
            ->addColumn('paid_at_formatted', fn ($transaction) => $transaction->paid_at?->format('d M Y H:i') ?? '-')
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    /**
     * Get query for users listing.
     */
    public function getUsersQuery(): Builder
    {
        return User::query()->with('roles');
    }

    public function getUsersDataTable()
    {
        $user = auth()->user();
        $query = $this->getUsersQuery();

        if ($user->hasAnyRole(['super admin', 'admin'])) {
            // Admin sees all
        } elseif ($user->hasRole('manager')) {
            // Managers can manage everyone except administrator accounts.
            $query->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['super admin', 'admin']);
            });
        } elseif ($user->can('admin.users.index')) {
            // Staff sees only Customers
            $query->role('customer');
        } else {
            // Fallback
            $query->where('id', 0);
        }

        return DataTables::eloquent($query)
            ->addColumn('role', function ($user) {
                $role = $user->getRoleNames()->first();

                return $role ? str($role)->headline() : 'N/A';
            })
            ->addColumn('total_orders', function ($user) {
                return $user->orders()->count();
            })
            ->addColumn('total_spent', function ($user) {
                return \format_price($user->orders()->sum('grand_total'));
            })
            ->addColumn('action', function ($user) {
                return view('admin.content.users.actions', compact('user'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getCustomersDataTable()
    {
        $query = User::query()->with('roles')->role('customer');

        return DataTables::eloquent($query)
            ->addColumn('total_orders', function ($user) {
                return $user->orders()->count();
            })
            ->addColumn('total_spent', function ($user) {
                return \format_price($user->orders()->sum('grand_total'));
            })
            ->addColumn('action', function ($user) {
                return view('admin.content.users.actions', compact('user'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Get query for sales returns listing.
     */
    public function getSalesReturnsQuery(): Builder
    {
        return ReturnModel::query()->with(['order', 'order.user']);
    }

     /**
     * Get newsletter subscribers datatable
     */
    public function getNewsletterSubscribersDataTable()
    {
        return DataTables::eloquent(NewsletterSubscriber::latest())
            ->addColumn('status_badge', function ($subscriber) {
                return $subscriber->status === 'subscribed'
                    ? "<span class='badge badge-success'>Subscribed</span>"
                    : "<span class='badge badge-secondary'>Unsubscribed</span>";
            })
            ->editColumn('subscribed_at', function ($subscriber) {
                return $subscriber->subscribed_at->format('d M Y, H:i');
            })
            ->addColumn('action', function ($subscriber) {
                return view('admin.content.newsletter.actions', compact('subscriber'));
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Get notifications datatable
     */
    public function getNotificationsDataTable()
    {
        $query = auth()->user()->notifications()->where('is_admin', true)->getQuery();

        return DataTables::eloquent($query)
            ->addColumn('message', function ($notification) {
                return $notification->data['message'] ?? 'No message';
            })
            ->addColumn('created_at_human', function ($notification) {
                return $notification->created_at->diffForHumans();
            })
            ->addColumn('action', function ($notification) {
                $markReadBtn = '';
                if (! $notification->read_at) {
                    $markReadBtn = '<a href="'.route('admin.notifications.markAsRead', $notification->id).'" class="btn btn-sm btn-light btn-icon" title="Mark as Read"><i class="mdi mdi-check"></i></a>';
                }

                $actionUrl = $notification->data['action_url'] ?? null;

                // Dynamically determine URL if missing
                if (! $actionUrl) {
                    if (str_contains($notification->type, 'OrderStatusChanged') && isset($notification->data['order_id'])) {
                        $order = Order::select('id')->find($notification->data['order_id']);
                        $actionUrl = $order ? route('admin.website-orders.show', $order) : null;
                    } elseif (str_contains($notification->type, 'NewReviewPending')) {
                        $actionUrl = route('admin.product-review.index');
                    } elseif (str_contains($notification->type, 'LowStockAlert')) {
                        $actionUrl = route('admin.manage-inventory.index', [
                            'store_id' => $notification->data['store_id'] ?? null,
                            'search' => $notification->data['product_name'] ?? null,
                        ]);
                    }
                }

                $viewBtn = '';
                if ($actionUrl) {
                    $viewBtn = '<a href="'.$actionUrl.'" class="btn btn-sm btn-primary btn-icon ms-1" title="View"><i class="mdi mdi-eye"></i></a>';
                }

                return $markReadBtn.$viewBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Get audit logs datatable
     */
    public function getAuditLogsDataTable()
    {
        $query = Activity::query()->with(['causer', 'subject']);

        return DataTables::eloquent($query)
            ->addColumn('causer_name', function ($activity) {
                return $activity->causer?->name ?? 'System';
            })
            ->addColumn('subject_name', function ($activity) {
                if (! $activity->subject_type) {
                    return '-';
                }

                $subject = class_basename($activity->subject_type);

                if ($activity->subject) {
                    $label = $activity->subject->name
                        ?? $activity->subject->title
                        ?? $activity->subject->order_number
                        ?? $activity->subject->id;

                    return $subject.' #'.$label;
                }

                return $subject.' #'.$activity->subject_id;
            })
            ->addColumn('properties_summary', function ($activity) {
                $properties = $activity->properties;

                if ($properties->isEmpty()) {
                    return '-';
                }

                return '<code class="small">'.e($properties->toJson(JSON_PRETTY_PRINT)).'</code>';
            })
            ->addColumn('created_at_human', function ($activity) {
                return $activity->created_at->diffForHumans();
            })
            ->editColumn('log_name', function ($activity) {
                return $activity->log_name ? str($activity->log_name)->headline() : '-';
            })
            ->editColumn('event', function ($activity) {
                return $activity->event ? str($activity->event)->headline() : '-';
            })
            ->rawColumns(['properties_summary'])
            ->make(true);
    }
}
