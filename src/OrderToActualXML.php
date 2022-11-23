<?php

add_action('woocommerce_order_status_completed', 'order_to_actual', 10, 1);
function order_to_actual($order_id)
{
    $order = wc_get_order($order_id);

    $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><root></root>");
    $transaction = $xml->addChild('Tranzakcio');

    $transaction->addChild('TranzakcioID', $order_id);
    $transaction->addChild('TranzakcioTipus', 0);
    $transaction->addChild('PartnerModositas', 0);

    $header = $transaction->addChild('BFejlec');

    $header->addChild('MozgasAlapID', 400);
    $header->addChild('Kiallitas', $order->get_date_completed()->date('Y.m.d'));
    $header->addChild('FizModID', get_payment_id($order->get_payment_method()));
    $header->addChild('BSorszam2', 'WEB-' . $order_id);
    $header->addChild('Devizanem', 'HUF');
    $header->addChild('BArfolyam', 1);

    $order_data = $order->get_data();
    $partner = $header->addChild('Partner');
    // $partner->addChild('PKod', $order_id);
    $partner->addChild('PNev', $order_data['billing']['last_name'] . ' ' . $order_data['billing']['first_name']);
    $partner->addChild('PIrszam', $order_data['billing']['postcode']);
    $partner->addChild('PHelyseg', $order_id);

    // $partner_address = $partner->addChild('PartnerCim');
    // $partner_address->addChild('CimTipus', $order_id);
    // $partner_address->addChild('CimNev', $order_id);
    // $partner_address->addChild('IrSzam', $order_id);
    // $partner_address->addChild('Helyseg', $order_id);

    foreach ($order->get_items() as $item_key => $item) {
        $row = $header->addChild('BSor');
        $product = $item->get_product();

        $row->addChild('Cikkszam', $product->get_sku());
        $row->addChild('Mennyiseg', $item->get_quantity());
        $row->addChild('Egysegar', $item->get_total());
    }


    $success = $xml->asXML( ROOT . '/Actual/import/in/WEB-' . $order_id . '.xml');
    $note = $success ? __('Rendelés sikeresen exportálva az ACTUAL rendszerbe.') : __('Hiba lépett fel a rendelés exportálása közben ACTUAL rendszerbe.');
    $order->add_order_note($note);
}