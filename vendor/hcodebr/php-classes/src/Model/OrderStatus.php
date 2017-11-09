<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class OrderStatus extends Model {

	const AGUARDANDO_PAGAMENTO =  1; // Pedido foi realizado mas não foi pago.
	const REALIZADO            =  2; // Pedido foi realizado e pago.
	const RECEBIDO             =  3; // Pedido recebido pela loja.
	const EM_PREPARACAO        =  4; // Pedido em preparação pela loja.
	const PENDENTE             =  5; // Pedido pendente ou com alguma restrição.
	const PENDENTE_PRONTO      =  6; // Pedido pronto, porém pendente.
	const PENDENTE_EM_PRODUCAO =  7; // Pedido em produção, porém pendente.
	const PRONTO               =  8; // Pedido pronto.
	const EM_ROTA_DE_ENTREGA   =  9; // Pedido em rota de entrega.
	const DEVOLVIDO            = 10; // Pedido devolvido.
	const CANCELADO            = 11; // Pedido cancelado.
	const ENTREGUE             = 12; // Pedido entregue.

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select( "select * from tb_ordersstatus order by desstatus" );

	}

}

?>