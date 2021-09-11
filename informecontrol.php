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
        new PluginReportsColumn('proveedorfield', __('Supplier')),
        new PluginReportsColumn('categoria_id', __('Category')),
        new PluginReportsColumn('dispositivo_id', __('Device')),
        new PluginReportsColumn('serviciofield', __('Service')),
        new PluginReportsColumn('dispositivo_serial', __('Serial')),
        new PluginReportsColumn('pesofield', __('Pesos')),
        new PluginReportsColumn('dolarefield', __('Dollar')),
        new PluginReportsColumn('oconefield', __('OC 1')),
        new PluginReportsColumn('octwofield', __('OC 2')),
        new PluginReportsColumn('hesonefield', __('HES 1')),
        new PluginReportsColumn('hestwofield', __('HES 2')),
        new PluginReportsColumn('facturaonefield', __('Factura 2')),
        new PluginReportsColumn('facturatwofield', __('Factura 1')),
        new PluginReportsColumn('fechafacturaonefield', __('Fecha [F1]')),
        new PluginReportsColumn('fechafacturatwofield', __('Fecha [F2]')),
        new PluginReportsColumn('trmfield', __('TRM')),
        new PluginReportsColumn('totalfield', __('Total')),
        
    ));
    /* Conflicto con los elementos asociados al ticket */
    $query = "SELECT `glpi_tickets`.`id`,
        DATE(`glpi_tickets`.`date`) AS date,
        `glpi_locations`.`name` AS locations_id,
        `glpi_plugin_fields_ticketreparacions`.`proveedorfield`,
        `glpi_itilcategories`.`name` AS categoria_id,
        `glpi_peripherals`.`name` AS dispositivo_id,
        `glpi_plugin_fields_ticketreparacions`.`serviciofield`,
        `glpi_peripherals`.`serial` AS dispositivo_serial,
        `glpi_plugin_fields_ticketreparacions`.`pesofield`,
        `glpi_plugin_fields_ticketreparacions`.`dolarefield`,
        `glpi_plugin_fields_ticketreparacions`.`oconefield`,
        `glpi_plugin_fields_ticketreparacions`.`octwofield`,
        `glpi_plugin_fields_ticketreparacions`.`hesonefield`,
        `glpi_plugin_fields_ticketreparacions`.`hestwofield`,
        `glpi_plugin_fields_ticketreparacions`.`facturaonefield`,
        `glpi_plugin_fields_ticketreparacions`.`facturatwofield`,
        `glpi_plugin_fields_ticketreparacions`.`fechafacturaonefield`,
        `glpi_plugin_fields_ticketreparacions`.`fechafacturatwofield`,
        `glpi_plugin_fields_ticketreparacions`.`trmfield`,
        `glpi_plugin_fields_ticketreparacions`.`totalfield`
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