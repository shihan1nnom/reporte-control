<?php

/* Comportamiento con respecto a la conexión a la base de datos */
$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

$dbu = new DbUtils();

$report= new PluginReportsAutoReport();

// Criterios de busqueda
new PluginReportsDateIntervalCriteria($report, '`glpi_tickets`.`date`', __('Opening date'));
$category = new PluginReportsTicketCategoryCriteria($report);
$category->setSqlField("`glpi_tickets`.`itilcategories_id`");

// se necesita el formulario de criterios de visualizacion
$report->displayCriteriasForm();

if ($report->criteriasValidated()) {
    $report->setSubNameAuto();

    // Nombre de las columnas que se mostraran
    $report->setColumns(array(
        new PluginReportsColumn('id', __('ID')),
        new PluginReportsColumnDateTime('date', __('Creation date'), ['sorton' => '`date`']),
        new PluginReportsColumn('locations_id', __('Store')),
        new PluginReportsColumn('proveedor', __('Supplier')),
        new PluginReportsColumn('categoria_id', __('Category')),
        new PluginReportsColumn('dispositivo_id', __('Device')),
        new PluginReportsColumn('servicio', __('Service')),
        new PluginReportsColumn('dispositivo_serial', __('Serial')),
        new PluginReportsColumn('pesos', __('Pesos')),
        new PluginReportsColumn('dolares', __('Dollar')),
        new PluginReportsColumn('oc1', __('OC 1')),
        new PluginReportsColumn('oc2', __('OC 2')),
        new PluginReportsColumn('hes1', __('HES 1')),
        new PluginReportsColumn('hes2', __('HES 2')),
        new PluginReportsColumn('factura1', __('Factura 2')),
        new PluginReportsColumn('factura2', __('Factura 1')),
        new PluginReportsColumn('fecha1', __('Fecha [F1]')),
        new PluginReportsColumn('fecha2', __('Fecha [F2]')),
        new PluginReportsColumn('trm', __('TRM')),
        new PluginReportsColumn('total', __('Total')),
        
    ));
    /* Ajustar nombres de atributos segun corresponda */
    $query = "SELECT `glpi_tickets`.`id`,
        DATE(`glpi_tickets`.`date`) AS date,
        `glpi_locations`.`name` AS locations_id,
        `glpi_plugin_fields_ticketreparacions`.`proveedorfield` AS proveedor,
        `glpi_itilcategories`.`name` AS categoria_id,
        `glpi_peripherals`.`name` AS dispositivo_id,
        `glpi_plugin_fields_ticketreparacions`.`serviciofield` AS servicio,
        `glpi_peripherals`.`serial` AS dispositivo_serial,
        `glpi_plugin_fields_ticketreparacions`.`pesofield` AS pesos,
        `glpi_plugin_fields_ticketreparacions`.`dolarefield` AS dolares,
        `glpi_plugin_fields_ticketreparacions`.`oconefield` AS oc1,
        `glpi_plugin_fields_ticketreparacions`.`octwofield` AS oc2,
        `glpi_plugin_fields_ticketreparacions`.`hesonefield` AS hes1,
        `glpi_plugin_fields_ticketreparacions`.`hestwofield` AS hes2,
        `glpi_plugin_fields_ticketreparacions`.`facturaonefield` AS factura1,
        `glpi_plugin_fields_ticketreparacions`.`facturatwofield` AS factura2,
        `glpi_plugin_fields_ticketreparacions`.`fechafacturaonefield` AS fecha1,
        `glpi_plugin_fields_ticketreparacions`.`fechafacturatwofield` AS fecha2,
        `glpi_plugin_fields_ticketreparacions`.`trmfield` AS trm,
        `glpi_plugin_fields_ticketreparacions`.`totalfield` AS total
        FROM `glpi_tickets`
        LEFT JOIN `glpi_locations`
        ON `glpi_locations`.`id` = `glpi_tickets`.`locations_id`
        LEFT JOIN `glpi_plugin_fields_ticketreparacions`
        ON `glpi_plugin_fields_ticketreparacions`.`items_id` = `glpi_tickets`.`id`
        INNER JOIN `glpi_itilcategories`
        ON `glpi_itilcategories`.`id` = `glpi_tickets`.`itilcategories_id`
        LEFT JOIN `glpi_items_tickets`
        ON (`glpi_items_tickets`.`tickets_id` = `glpi_tickets`.`id` AND `glpi_items_tickets`.`itemtype` = 'Peripheral')
        LEFT JOIN `glpi_peripherals`
        ON (`glpi_peripherals`.`id` = `glpi_items_tickets`.`items_id`)
        WHERE NOT `glpi_tickets`.`is_deleted`
        ".$report->addSqlCriteriasRestriction().$report->getOrderBy('date');
    
    $report->setSqlRequest($query);
    $report->execute();
} else {
    Html::footer();
}