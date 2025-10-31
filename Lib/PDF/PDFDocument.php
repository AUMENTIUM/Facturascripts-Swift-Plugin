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
        if (false === $payMethod->load($receipt->codpago)) {
            // forma de pago no encontrada
            return '-';
        }

        if (false === $payMethod->imprimir) {
            // no imprimir información bancaria
            return $payMethod->descripcion;
        }
        
        
        // Domiciliado. Mostramos la cuenta bancaria del cliente
        $cuentaBcoCli = new CuentaBancoCliente();
        $where = [new DataBaseWhere('codcliente', $receipt->codcliente)];
        if ($payMethod->domiciliado && $cuentaBcoCli->loadWhere($where, ['principal' => 'DESC'])) {
            return $payMethod->descripcion . ' : ' . $cuentaBcoCli->getIban(true, true);
        }

        // cuenta bancaria de la empresa
        $cuentaBco = new CuentaBanco();
        if ($payMethod->codcuentabanco && $cuentaBco->load($payMethod->codcuentabanco) && $cuentaBco->iban) {
            $swift = empty($cuentaBco->getSwift())? ' ' : ' Swift: '.$cuentaBco->getSwift();
            $iban = $cuentaBco->getIban(true);
            $blocks = explode(' ', $iban);
            return $payMethod->descripcion . ' : ' .  $iban . ' (' . $this->i18n->trans('last-block') . ' ' . end($blocks) . ')'. $swift;
        }
        
        // no hay información bancaria
        return $payMethod->descripcion;
    }
}
