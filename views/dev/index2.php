<div class="products container">
    <input class="form-control fast-search-input" id="filter_input_product" placeholder="Пошук товару">
    <div class="products scroll">
        <ul class="list-group fast-search-list" id="filter_list_product">
            
            <? if($products) foreach($products as $product):?>
                <li class="product"> 
                    <div><?=$product['name']?></div> 
                </li>
            <? endforeach; ?>
        </ul>
    </div>
</div>