<?php 

namespace FacturaScripts\Plugins\Swift\Lib\PDF;


use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Dinamic\Model\CuentaBanco;
use FacturaScripts\Dinamic\Model\CuentaBancoCliente;
use FacturaScripts\Dinamic\Model\FormaPago;

/**
 * Class created by AUMENTIUM
 * 
 * @author AUMENTIUM <jgarces@aumentium.com>
 */
abstract class PDFDocument extends \FacturaScripts\Core\Lib\PDF\PDFDocument
{
    /**
     * 
     * @param BusinessDocument|ReciboCliente $receipt
     *
     * @return string
     */
    protected function getBankData($receipt): string
    {
        $payMethod = new FormaPago();
        if (false === $payMethod->loadFromCode($receipt->codpago)) {
            return '-';
        }

        $cuentaBcoCli = new CuentaBancoCliente();
        $where = [new DataBaseWhere('codcliente', $receipt->codcliente)];
        if ($payMethod->domiciliado && $cuentaBcoCli->loadFromCode('', $where, ['principal' => 'DESC'])) {
            return $payMethod->descripcion . ' : ' . $cuentaBcoCli->getIban(true, true);
        }

        $cuentaBco = new CuentaBanco();
        if (empty($payMethod->codcuentabanco) || false === $cuentaBco->loadFromCode($payMethod->codcuentabanco) || empty($cuentaBco->iban)) {
            return $payMethod->descripcion;
        }

        $iban = $cuentaBco->getIban(true);
        $swift = empty($cuentaBco->getSwift())? ' ' : ' Swift: '.$cuentaBco->getSwift();
        $blocks = explode(' ', $iban);
        return $payMethod->descripcion . ' : ' . $iban . ' (' . $this->i18n->trans('last-block') . ' ' . end($blocks) . ')'. $swift;
    }
}
