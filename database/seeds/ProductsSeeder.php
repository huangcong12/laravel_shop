<?php

use App\Product;
use App\ProductSku;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 创建 30 个商品
        $products = factory(Product::class, 1)->create();
        foreach ($products as $product) {
            // 每个商品生成 3 个 sku
            $skus = factory(ProductSku::class, 3)->create([
                'product_id' => $product->id,
            ]);

            // 找出价格最低的 sku，然后保存给商品当价格
            $product->save(['price' => $skus->min('price')]);
        }
    }
}
