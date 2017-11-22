DROP PROCEDURE IF EXISTS `sp_orderspagseguro_save`;

DELIMITER ;;
CREATE PROCEDURE `sp_orderspagseguro_save`(
pidorder int, 
pdescode varchar(36), 
pvlgrossamount decimal(10,2), 
pvldiscountamount decimal(10,2), 
pvlfeeamount decimal(10,2), 
pvlnetamount decimal(10,2), 
pvlextraamount decimal(10,2),
pdespaymentlink varchar(256)
)
BEGIN
	
    DELETE FROM tb_orderspagseguro WHERE idorder = pidorder;
    
    INSERT INTO tb_orderspagseguro (idorder, descode, vlgrossamount, vldiscountamount, vlfeeamount, vlnetamount, vlextraamount, despaymentlink)
	VALUES(pidorder, pdescode, pvlgrossamount, pvldiscountamount, pvlfeeamount, pvlnetamount, pvlextraamount, pdespaymentlink);
    
END ;;
DELIMITER ;