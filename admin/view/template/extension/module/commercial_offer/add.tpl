<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-commercial-offer" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
            
        <div class="panel panel-default">
            <div class="panel-heading">
                <!-- <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3> -->
                <h3 class="panel-title"><i class="fa fa-pencil"></i> dobavit</h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-commercial-offer" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="commercial_offer_list_name">Название</label>
                        <div class="col-sm-10">
                          <input type="text" name="name" value="" placeholder="Название" id="commercial_offer_list_name" class="form-control" />
                          <!-- <?php if (isset($error_name[$language['language_id']])) { ?> -->
                          <!-- <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div> -->
                          <!-- <?php } ?> -->
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="commercial_offer_list_categories">Категории</label>
                        <div class="col-sm-10">
                          <input type="text" name="category" value="" placeholder="Категории" id="commercial_offer_list_categories" class="form-control" />
                          <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
                            <?php foreach ($product_categories as $product_category) { ?>
                                <div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                                <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                                </div>
                            <?php } ?>
                          </div>
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo $entry_category; ?></span></label>
                        <div class="col-sm-10">
                          <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
                          <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
                            <?php foreach ($product_categories as $product_category) { ?>
                            <div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                              <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                            </div>
                            <?php } ?>
                          </div>
                        </div>
                    </div> -->



                    <!-- ATTRIBUTES -->
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="commercial_offer_list_attributes">Атрибуты</label>
                        <div class="col-sm-10">
                          <input type="text" name="attribute" value="" placeholder="Атрибуты" id="commercial_offer_list_attributes" class="form-control" />
                          <div id="category-attribute" class="well well-sm" style="height: 150px; overflow: auto;">
                            <?php foreach ($product_attributes as $product_attribute) { ?>
                                <div id="category-attribute<?php echo $product_attribute['attribute_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_attribute['name']; ?>
                                    <input type="hidden" name="product_attribute[]" value="<?php echo $product_attribute['attribute_id']; ?>" />
                                </div>
                            <?php } ?>
                          </div>
                        </div>
                    </div>
                    <!-- <input type="text" name="product_attribute[<?php echo $attribute_row; ?>][name]" value="<?php echo $product_attribute['name']; ?>" placeholder="<?php echo $entry_attribute; ?>" class="form-control" /> -->
                        <!-- <input type="hidden" name="product_attribute[<?php echo $attribute_row; ?>][attribute_id]" value="<?php echo $product_attribute['attribute_id']; ?>" /> -->
                    

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status">Статус</label>
                        <div class="col-sm-10">
                        <select name="status" id="input-status" class="form-control">
                            <?php if ($status) { ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                        </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>


<script>
$('input[name=\'category\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'category\']').val('');

		$('#product-category' + item['value']).remove();

		$('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
	}
});

$('#product-category').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

$('input[name=\'attribute\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/attribute/autocomplete&<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
                response($.map(json, function(item) {
                    return {
                        category: item.attribute_group,
                        label: item.name,
                        value: item.attribute_id
                    }
                }));
			}
		});
	},
	'select': function(item) {
        console.log(2);
		$('input[name=\'attribute\']').val('');

		$('#category-attribute' + item['value']).remove();

		$('#category-attribute').append('<div id="category-attribute' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_attribute[]" value="' + item['value'] + '" /></div>');
	}
});

$('#category-attribute').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
</script>
