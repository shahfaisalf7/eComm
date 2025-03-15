<?php

namespace Modules\DeliveryCharge\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    public function extend(Menu $menu)
    {
        $menu->group(trans('admin::sidebar.content'), function (Group $group) {
            $group->item("Charges", function (Item $item) {
                $item->icon('fa fa-money');
                $item->weight(16);

                $item->item("Delivery Charges", function (Item $item) {
                    $item->weight(1);
                    $item->route('admin.delivery.charge');
                });
                $item->item("Product Charges", function (Item $item) {
                    $item->weight(2);
                    $item->route('admin.product.charge');
                });
                $item->item("Box Charges", function (Item $item) {
                    $item->weight(3);
                    $item->route('admin.box.charge');
                });
            });
        });
    }
}
