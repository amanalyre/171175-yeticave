    <li class="lots__item lot">
    <div class="lot__image">
        <img src=<?=htmlspecialchars($img_url) ?> width="350" height="260" alt="Сноуборд">
    </div>
    <div class="lot__info">
        <span class="lot__category"><?=htmlspecialchars($cat_name) ?></span>
        <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$id ?>"><?=$lot_name ?></a></h3>
        <div class="lot__state">
            <div class="lot__rate">
                <span class="lot__amount">Стартовая цена</span>
                <span class="lot__cost"><?=price_round($start_price);?></b></span>
            </div>
            <div class="lot__timer timer">
                <?=((23 - date('G')) . ':' . (60 - date('i')));?>
            </div>
        </div>
    </div>
    </li>