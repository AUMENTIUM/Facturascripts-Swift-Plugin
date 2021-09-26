<?php 

namespace FacturaScripts\Plugins\Swift\Model;

/**
 * Class created by AUMENTIUM
 * 
 * @author AUMENTIUM <jgarces@aumentium.com>
 */
class CuentaBanco extends \FacturaScripts\Core\Model\CuentaBanco
{
    
    /**
     * Returns the SWIFT code if exist.
     *
     *
     * @return string
     */
    public function getSwift(): string
    {
        return empty($this->swift) ? '' : $this->swift;
    }
    
}
