<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
        <div class="pull-right">
            <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-commercial-offer').submit() : false;"><i class="fa fa-trash-o"></i></button>
        </div>
        
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <!-- <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3> -->
            <h3 class="panel-title"><i class="fa fa-list"></i> Тест</h3>
        </div>
        <div class="panel-body">
            <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-commercial-offer">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                                <td class="text-left">
                                    <span>
                                        Название списка
                                        <!-- <?php echo $column_name; ?> -->
                                    </span>
                                </td>
                                <td class="text-left">
                                    <span>
                                        Статус
                                        <!-- <?php echo $column_name; ?> -->
                                    </span>
                                </td>
                                <td class="text-left">
                                    <span>
                                        Добавлено
                                        <!-- <?php echo $column_name; ?> -->
                                    </span>
                                </td>
                            <td class="text-right">
                                        Действие
                                    <!-- <?php echo $column_action; ?>  -->
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($commercial_offers) { ?>
                                <?php foreach ($commercial_offers as $commercial_offer) { ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if (in_array($commercial_offer['commercial_offer_id'], $selected)) { ?>
                                                <input type="checkbox" name="selected[]" value="<?php echo $commercial_offer['commercial_offer_id']; ?>" checked="checked" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="selected[]" value="<?php echo $commercial_offer['commercial_offer_id']; ?>" />
                                            <?php } ?>
                                        </td>
                                        <td class="text-left">
                                            <?php echo $commercial_offer['name']; ?>
                                        </td>
                                        <td class="text-left">
                                            <?php echo $commercial_offer['is_active'] ? $text_enabled : $text_disabled; ?>    
                                        </td>
                                        <td class="text-left">
                                            <?php echo $commercial_offer['date_added']; ?>    
                                        </td>
                                        <td class="text-right">
                                            <a href="<?php echo $commercial_offer['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>

<?php echo $footer; ?>
