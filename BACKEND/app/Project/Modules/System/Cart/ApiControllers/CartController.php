<?php

namespace App\Project\Modules\System\Cart\ApiControllers;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Cart\Requests\Api\AddToCartRequest;
use App\Project\Modules\System\Cart\Requests\Api\UpdateCartItemRequest;
use App\Project\Modules\System\Cart\Services\Api\AddToCartAction;
use App\Project\Modules\System\Cart\Services\Api\UpdateCartItemAction;
use App\Project\Modules\System\Cart\Services\Api\RemoveFromCartAction;
use App\Project\Modules\System\Cart\Services\Api\ClearCartAction;
use App\Project\Modules\System\Cart\Services\Api\GetUserCartAction;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(AddToCartRequest $request, AddToCartAction $addToCartAction)
    {
        return $addToCartAction->handle($request);
    }

    public function updateCartItem($cartItemUuid, UpdateCartItemRequest $request, UpdateCartItemAction $updateCartItemAction)
    {
        return $updateCartItemAction->handle($request, $cartItemUuid);
    }

    public function removeFromCart(Request $request, $cartItemUuid, RemoveFromCartAction $removeFromCartAction)
    {
        return $removeFromCartAction->handle($cartItemUuid, $request);
    }

    public function clearCart(Request $request, ClearCartAction $clearCartAction)
    {
        return $clearCartAction->handle($request);
    }

    public function getUserCart(Request $request, GetUserCartAction $getUserCartAction)
    {
        return $getUserCartAction->handle($request);
    }
}
