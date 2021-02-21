vendor/bin/ecs check --fix && vendor/bin/psalm --show-info=false && ./vendor/bin/phpstan analyse && ./vendor/bin/sail artisan test

## Tests
./vendor/bin/sail artisan migrate:fresh --seed 
./vendor/bin/sail artisan test

## Would normally have refactored

## Would normally have done differently
    Design session
    Not a fan of enumerators. Purchasable has class/product which we commonly refer to as Products with goods/services options. Change to table with FKs
    pricing_adjustments - json vs fields => assertDatabaseHas && validation
    if 2 adjustments both create the lowest price, flag first one as the method used to chaneg the value