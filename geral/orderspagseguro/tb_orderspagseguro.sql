--
-- Table structure for table `tb_orderspagseguro`
--

DROP TABLE IF EXISTS `tb_orderspagseguro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_orderspagseguro` (
  `idorder` int(11) NOT NULL,
  `descode` varchar(36) NOT NULL,
  `vlgrossamount` decimal(10,2) NOT NULL,
  `vldiscountamount` decimal(10,2) NOT NULL,
  `vlfeeamount` decimal(10,2) NOT NULL,
  `vlnetamount` decimal(10,2) NOT NULL,
  `vlextraamount` decimal(10,2) NOT NULL,
  `despaymentlink` varchar(256) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_orderspagseguro`
--

LOCK TABLES `tb_orderspagseguro` WRITE;
/*!40000 ALTER TABLE `tb_orderspagseguro` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_orderspagseguro` ENABLE KEYS */;
UNLOCK TABLES;