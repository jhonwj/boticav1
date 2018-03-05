alter table Ve_DocVentaTipoDoc add column LimiteItems INT;
update Ve_DocVentaTipoDoc set LimiteItems = 7 where TipoDoc = 'FACTURA';
update Ve_DocVentaTipoDoc set LimiteItems = 6 where TipoDoc = 'BOLETA';
update Ve_DocVentaTipoDoc set LimiteItems = 0 where TipoDoc = 'TICKET';
update Ve_DocVentaTipoDoc set LimiteItems = 0 where TipoDoc = 'NO VALIDO';
update Ve_DocVentaTipoDoc set LimiteItems = 7 where TipoDoc = 'FACTURA/IGV';
