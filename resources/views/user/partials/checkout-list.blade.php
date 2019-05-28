<table class="shopping-cart-table table table-responsive">
    <thead>
        <tr>
            <th>@lang('custom.common.product')</th>
            <th></th>
            <th class="text-center">@lang('custom.common.price')</th>
            <th class="text-center">@lang('custom.common.qty')</th>
            <th class="text-center">@lang('custom.common.total')</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cart as $item)
            <tr data-id="{{ $item->rowId }}">
                <td class="thumb">
                    {{ Html::image($item->options->image, 'a picture') }}
                </td>
                <td class="details">
                    {!! Html::linkRoute(
                        'product',
                        $item->name,
                        $item->id
                    ) !!}
                </td>
                <td class="price text-center">
                    <strong>{{ number_format($item->price, 0, '', '.') }} @lang('custom.common.currency')</strong>
                    @if ($item->price != $item->options->intPrice)
                        <br>
                        <del class="font-weak">
                            <small>{{ number_format($item->options->intPrice, 0, '', '.') }} @lang('custom.common.currency')</small>
                        </del>
                    @endif
                </td>
                <td class="qty text-center">
                    {!! Form::number(
                        'product' . $item->id,
                        $item->qty,
                        [
                            'class' => 'input form-control qty-checkout',
                            'data-id' => $item->rowId,
                            'data-product-id' => $item->id,
                            'min' => 1,
                            'style' => 'display: inline-block;'
                        ]
                    ) !!}
                    <p class="qty-available" data-product-id="{{ $item->id }}" style="display: inline-block;">
                        @lang('custom.common.availability', ['qty' => $item->options->intQty])
                    </p>
                </td>
                <td class="total text-center">
                    <strong class="primary-color">{{ number_format($item->subtotal, 0, '', '.') }} @lang('custom.common.currency')</strong>
                </td>
                <td class="text-right">
                    {!! html_entity_decode(
                        Form::button(
                            '<i class="fa fa-close"></i>',
                            [
                                'class' => 'cancel-btn main-btn icon-btn',
                                'data-id' => $item->rowId,
                            ]
                        )
                    ) !!}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @if(Auth::user() && Auth::user()->discount > 0)
        <tr>
            <th class="empty" colspan="3"></th>
            <th>Discount</th>
            <th colspan="2" class="total" id="total-cart">{{ Auth::user()->discount }} %</th>
        </tr>
        <tr>
            <th class="empty" colspan="3"></th>
            <th>@lang('custom.common.total')</th>
            <th colspan="2" class="total" id="total-cart">{{ number_format(Cart::total() - ((Auth::user()->discount * Cart::total())/100), 0, '', '.') }} @lang('custom.common.currency')</th>
        </tr>
        @else
        <tr>
            <th class="empty" colspan="3"></th>
            <th>@lang('custom.common.total')</th>
            <th colspan="2" class="total" id="total-cart">{{ number_format(Cart::total(), 0, '', '.') }} @lang('custom.common.currency')</th>
        </tr>
        @endif
    </tfoot>
</table>
