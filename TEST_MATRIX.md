# Test Matrix (Detailed)

Total Feature scenarios: 90

| ID | Module | Automated Test | File | Type | Expected |
|---|---|---|---|---|---|
| F-001 | DriverTest | test_create_driver_success | tests/Feature/DriverTest.php:15 | Positive | Success status (2xx) |
| F-002 | DriverTest | test_driver_cannot_be_deleted_if_has_vehicle | tests/Feature/DriverTest.php:28 | Negative | Validation or error status (4xx) |
| F-003 | DriverTest | test_driver_assign_vehicle_success | tests/Feature/DriverTest.php:40 | Positive | Success status (2xx) |
| F-004 | DriverTest | test_driver_assign_fails_if_not_available | tests/Feature/DriverTest.php:56 | Negative | Validation or error status (4xx) |
| F-005 | DriverTest | test_unassign_driver_success | tests/Feature/DriverTest.php:71 | Positive | Success status (2xx) |
| F-006 | DriverTest | test_cannot_unassign_driver_without_vehicle | tests/Feature/DriverTest.php:84 | Negative | Validation or error status (4xx) |
| F-007 | DriverTest | test_restore_driver_success | tests/Feature/DriverTest.php:93 | Positive | Success status (2xx) |
| F-008 | DriverTest | test_restore_driver_not_found | tests/Feature/DriverTest.php:103 | Negative | Validation or error status (4xx) |
| F-009 | DriverTest | test_filter_drivers_by_available_true | tests/Feature/DriverTest.php:109 | Positive | Success status (2xx) |
| F-010 | DriverTest | test_filter_drivers_by_available_false | tests/Feature/DriverTest.php:119 | Positive | Success status (2xx) |
| F-011 | DriverTest | test_show_deleted_drivers | tests/Feature/DriverTest.php:128 | Positive | Success status (2xx) |
| F-012 | DriverTest | test_update_driver | tests/Feature/DriverTest.php:138 | Positive | Success status (2xx) |
| F-013 | DriverTest | test_driver_index_without_filters | tests/Feature/DriverTest.php:148 | Negative | Validation or error status (4xx) |
| F-014 | DriverTest | test_driver_show_not_found | tests/Feature/DriverTest.php:157 | Negative | Validation or error status (4xx) |
| F-015 | DriverTest | test_driver_update_not_found | tests/Feature/DriverTest.php:164 | Negative | Validation or error status (4xx) |
| F-016 | DriverTest | test_driver_delete_not_found | tests/Feature/DriverTest.php:173 | Negative | Validation or error status (4xx) |
| F-017 | DriverTest | test_driver_assign_not_found | tests/Feature/DriverTest.php:180 | Negative | Validation or error status (4xx) |
| F-018 | FleetTest | test_create_fleet | tests/Feature/FleetTest.php:14 | Positive | Success status (2xx) |
| F-019 | FleetTest | test_cannot_delete_fleet_with_vehicles | tests/Feature/FleetTest.php:24 | Negative | Validation or error status (4xx) |
| F-020 | FleetTest | test_delete_fleet_success | tests/Feature/FleetTest.php:37 | Positive | Success status (2xx) |
| F-021 | FleetTest | test_can_list_fleets | tests/Feature/FleetTest.php:45 | Positive | Success status (2xx) |
| F-022 | FleetTest | test_can_show_fleet | tests/Feature/FleetTest.php:54 | Positive | Success status (2xx) |
| F-023 | FleetTest | test_restore_fleet_success | tests/Feature/FleetTest.php:63 | Positive | Success status (2xx) |
| F-024 | FleetTest | test_update_fleet | tests/Feature/FleetTest.php:72 | Positive | Success status (2xx) |
| F-025 | FleetTest | test_cannot_delete_non_existing_fleet | tests/Feature/FleetTest.php:83 | Negative | Validation or error status (4xx) |
| F-026 | FleetTest | test_filter_deleted_fleets | tests/Feature/FleetTest.php:90 | Positive | Success status (2xx) |
| F-027 | FleetTest | test_create_fleet_without_name_fails | tests/Feature/FleetTest.php:100 | Negative | Validation or error status (4xx) |
| F-028 | FleetTest | test_create_fleet_invalid_type | tests/Feature/FleetTest.php:107 | Negative | Validation or error status (4xx) |
| F-029 | FleetTest | test_show_non_existing_fleet | tests/Feature/FleetTest.php:117 | Negative | Validation or error status (4xx) |
| F-030 | FleetTest | test_fleet_index_without_filters | tests/Feature/FleetTest.php:123 | Negative | Validation or error status (4xx) |
| F-031 | FleetTest | test_fleet_update_not_found | tests/Feature/FleetTest.php:132 | Negative | Validation or error status (4xx) |
| F-032 | FleetTest | test_fleet_restore_not_found | tests/Feature/FleetTest.php:141 | Negative | Validation or error status (4xx) |
| F-033 | FleetTest | test_fleet_delete_twice | tests/Feature/FleetTest.php:148 | Negative | Validation or error status (4xx) |
| F-034 | FleetTest | test_fleet_create_with_description | tests/Feature/FleetTest.php:158 | Positive | Success status (2xx) |
| F-035 | FuelSupplyTest | test_create_fuel_supply_success | tests/Feature/FuelSupplyTest.php:15 | Positive | Success status (2xx) |
| F-036 | FuelSupplyTest | test_cannot_update_completed_supply | tests/Feature/FuelSupplyTest.php:35 | Negative | Validation or error status (4xx) |
| F-037 | FuelSupplyTest | test_can_list_fuel_supplies | tests/Feature/FuelSupplyTest.php:45 | Positive | Success status (2xx) |
| F-038 | FuelSupplyTest | test_can_show_fuel_supply | tests/Feature/FuelSupplyTest.php:54 | Positive | Success status (2xx) |
| F-039 | FuelSupplyTest | test_delete_fuel_supply | tests/Feature/FuelSupplyTest.php:63 | Positive | Success status (2xx) |
| F-040 | FuelSupplyTest | test_filter_fuel_by_vehicle | tests/Feature/FuelSupplyTest.php:71 | Positive | Success status (2xx) |
| F-041 | FuelSupplyTest | test_filter_fuel_by_route | tests/Feature/FuelSupplyTest.php:80 | Positive | Success status (2xx) |
| F-042 | FuelSupplyTest | test_filter_fuel_by_date | tests/Feature/FuelSupplyTest.php:89 | Positive | Success status (2xx) |
| F-043 | FuelSupplyTest | test_restore_fuel_supply | tests/Feature/FuelSupplyTest.php:100 | Positive | Success status (2xx) |
| F-044 | FuelSupplyTest | test_restore_fuel_not_found | tests/Feature/FuelSupplyTest.php:110 | Negative | Validation or error status (4xx) |
| F-045 | FuelSupplyTest | test_create_fuel_without_relation_fails | tests/Feature/FuelSupplyTest.php:117 | Negative | Validation or error status (4xx) |
| F-046 | FuelSupplyTest | test_update_fuel_supply | tests/Feature/FuelSupplyTest.php:128 | Positive | Success status (2xx) |
| F-047 | FuelSupplyTest | test_fuel_supply_index_without_filters | tests/Feature/FuelSupplyTest.php:138 | Negative | Validation or error status (4xx) |
| F-048 | FuelSupplyTest | test_fuel_supply_show_not_found | tests/Feature/FuelSupplyTest.php:147 | Negative | Validation or error status (4xx) |
| F-049 | FuelSupplyTest | test_fuel_supply_delete_not_found | tests/Feature/FuelSupplyTest.php:154 | Negative | Validation or error status (4xx) |
| F-050 | FuelSupplyTest | test_fuel_supply_update_not_found | tests/Feature/FuelSupplyTest.php:161 | Negative | Validation or error status (4xx) |
| F-051 | FuelSupplyTest | test_fuel_supply_default_values | tests/Feature/FuelSupplyTest.php:170 | Positive | Success status (2xx) |
| F-052 | RoleTest | test_create_role | tests/Feature/RoleTest.php:13 | Positive | Success status (2xx) |
| F-053 | RoleTest | test_delete_role | tests/Feature/RoleTest.php:22 | Positive | Success status (2xx) |
| F-054 | RoleTest | test_can_list_roles | tests/Feature/RoleTest.php:30 | Positive | Success status (2xx) |
| F-055 | RoleTest | test_can_show_role | tests/Feature/RoleTest.php:39 | Positive | Success status (2xx) |
| F-056 | RoleTest | test_restore_role | tests/Feature/RoleTest.php:48 | Positive | Success status (2xx) |
| F-057 | RoleTest | test_update_role | tests/Feature/RoleTest.php:57 | Positive | Success status (2xx) |
| F-058 | RoleTest | test_delete_non_existing_role | tests/Feature/RoleTest.php:68 | Negative | Validation or error status (4xx) |
| F-059 | RoleTest | test_filter_roles_by_name | tests/Feature/RoleTest.php:75 | Positive | Success status (2xx) |
| F-060 | RoleTest | test_restore_role_not_found | tests/Feature/RoleTest.php:84 | Negative | Validation or error status (4xx) |
| F-061 | RoleTest | test_role_index_without_filters | tests/Feature/RoleTest.php:90 | Negative | Validation or error status (4xx) |
| F-062 | RoleTest | test_role_show_not_found | tests/Feature/RoleTest.php:99 | Negative | Validation or error status (4xx) |
| F-063 | RoleTest | test_role_update_not_found | tests/Feature/RoleTest.php:106 | Negative | Validation or error status (4xx) |
| F-064 | RoleTest | test_role_create_without_name | tests/Feature/RoleTest.php:115 | Negative | Validation or error status (4xx) |
| F-065 | VehicleRouteTest | test_create_vehicle_route_success | tests/Feature/VehicleRouteTest.php:14 | Positive | Success status (2xx) |
| F-066 | VehicleRouteTest | test_create_vehicle_route_insufficient_fuel | tests/Feature/VehicleRouteTest.php:38 | Positive | Success status (2xx) |
| F-067 | VehicleRouteTest | test_can_list_vehicle_routes | tests/Feature/VehicleRouteTest.php:61 | Positive | Success status (2xx) |
| F-068 | VehicleRouteTest | test_cannot_create_if_vehicle_not_available | tests/Feature/VehicleRouteTest.php:70 | Negative | Validation or error status (4xx) |
| F-069 | VehicleRouteTest | test_delete_vehicle_route_success | tests/Feature/VehicleRouteTest.php:88 | Positive | Success status (2xx) |
| F-070 | VehicleRouteTest | test_show_vehicle_route | tests/Feature/VehicleRouteTest.php:98 | Positive | Success status (2xx) |
| F-071 | VehicleRouteTest | test_update_vehicle_route_success | tests/Feature/VehicleRouteTest.php:107 | Positive | Success status (2xx) |
| F-072 | VehicleRouteTest | test_update_vehicle_route_invalid_status | tests/Feature/VehicleRouteTest.php:120 | Negative | Validation or error status (4xx) |
| F-073 | VehicleRouteTest | test_restore_vehicle_route_success | tests/Feature/VehicleRouteTest.php:133 | Positive | Success status (2xx) |
| F-074 | VehicleTest | test_create_vehicle_success | tests/Feature/VehicleTest.php:14 | Positive | Success status (2xx) |
| F-075 | VehicleTest | test_cannot_assign_driver_if_not_available | tests/Feature/VehicleTest.php:33 | Negative | Validation or error status (4xx) |
| F-076 | VehicleTest | test_can_get_vehicles_list | tests/Feature/VehicleTest.php:54 | Positive | Success status (2xx) |
| F-077 | VehicleTest | test_can_show_vehicle | tests/Feature/VehicleTest.php:63 | Positive | Success status (2xx) |
| F-078 | VehicleTest | test_update_vehicle | tests/Feature/VehicleTest.php:72 | Positive | Success status (2xx) |
| F-079 | VehicleTest | test_filter_vehicle_by_status | tests/Feature/VehicleTest.php:82 | Positive | Success status (2xx) |
| F-080 | VehicleTest | test_filter_vehicle_by_plate | tests/Feature/VehicleTest.php:91 | Positive | Success status (2xx) |
| F-081 | VehicleTest | test_filter_vehicle_by_year | tests/Feature/VehicleTest.php:100 | Positive | Success status (2xx) |
| F-082 | VehicleTest | test_restore_vehicle_success | tests/Feature/VehicleTest.php:109 | Positive | Success status (2xx) |
| F-083 | VehicleTest | test_restore_vehicle_not_found | tests/Feature/VehicleTest.php:119 | Negative | Validation or error status (4xx) |
| F-084 | VehicleTest | test_delete_vehicle | tests/Feature/VehicleTest.php:126 | Positive | Success status (2xx) |
| F-085 | VehicleTest | test_vehicle_without_driver | tests/Feature/VehicleTest.php:135 | Negative | Validation or error status (4xx) |
| F-086 | VehicleTest | test_vehicle_index_without_filters | tests/Feature/VehicleTest.php:141 | Negative | Validation or error status (4xx) |
| F-087 | VehicleTest | test_vehicle_show_not_found | tests/Feature/VehicleTest.php:150 | Negative | Validation or error status (4xx) |
| F-088 | VehicleTest | test_vehicle_update_not_found | tests/Feature/VehicleTest.php:157 | Negative | Validation or error status (4xx) |
| F-089 | VehicleTest | test_vehicle_delete_not_found | tests/Feature/VehicleTest.php:166 | Negative | Validation or error status (4xx) |
| F-090 | VehicleTest | test_vehicle_filter_by_type | tests/Feature/VehicleTest.php:173 | Positive | Success status (2xx) |
