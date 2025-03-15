<?php

namespace Modules\GeoLocation\Sidebar;

use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Maatwebsite\Sidebar\Group;
use Modules\Admin\Sidebar\BaseSidebarExtender;

class SidebarExtender extends BaseSidebarExtender
{
    public function extend(Menu $menu)
    {
        $menu->group(trans('admin::sidebar.content'), function (Group $group) {
            $group->item("Geo Location", function (Item $item) {
                $item->icon('fa fa-map-marker');
                $item->weight(15);

                $item->item("Zones", function (Item $item) {
                    $item->weight(1);
                    $item->route('admin.geo.zones.sidebar');
                });
                $item->item("Cities", function (Item $item) {
                    $item->weight(2);
                    $item->route('admin.geo.cities.sidebar');
                });
                $item->item("Divisions", function (Item $item) {
                    $item->weight(3);
                    $item->route('admin.geo.division.sidebar');
                });
            });
        });
    }
}
