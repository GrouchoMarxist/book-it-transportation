<table class="form-table">
    <tbody>
      <tr>
        <td valign="top"><label for="outsource_company"><?php echo __('Outsource Company', 'bookit') ?></label></td>
        <td valign="top">
          <select id="outsource_company" name="tax_input[outsource_companies][]">
            <option value=""><?php echo __('In-House (not outsourced)', 'bookit') ?></option>
            <?php foreach ($terms as $tag) { ?>
            <option value="<?php echo $tag->term_id ?>" <?php if ($outsource[0]->term_id === $tag->term_id): ?>selected="selected"<?php endif; ?>><?php echo $tag->name ?></option>
            <?php } ?>
        </select>
        </td>
      </tr>
    <? foreach($bookit_config['fields'] as $key=>$value): ?>
      <tr>
        <td valign="top"><label for="<?=$value['key'] ?>"><?php _e( $value['name'], 'bookit' ); ?></label></td>
        <td valign="top">
          <?
          if($value['type'] === 'select') {
            ?>
            <select name="<?=$value['key'] ?>" id="<?=$value['key'] ?>">
              <? foreach($value['options'] as $k=>$v): ?>
              <option value="<?=$k ?>" <? if(get_post_meta( $object->ID, $value['key'], true ) == $k): ?>selected="selected"<? endif; ?>><?=$v ?></option>
              <? endforeach; ?>
            </select>
            <?
          } elseif($value['type'] === 'text' || $value['type'] === 'number'  || $value['type'] === 'tel' || $value['type'] === 'email') {
            ?>
            <input class="<?=$value['class'] ?>" type="<?=$value['type'] ?>" name="<?=$value['key'] ?>" id="<?=$value['key'] ?>" value="<?php echo esc_attr( get_post_meta( $object->ID, $value['key'], true ) ); ?>" <? if($value['type'] === 'number'): ?>min="0"<? endif; ?>>
            <?
          } elseif($value['type'] === 'textarea') {
            ?>
            <textarea name="<?=$value['key'] ?>" id="<?=$value['key'] ?>" cols="50" rows="5" class="<?=$value['class'] ?>"><?php echo esc_attr( get_post_meta( $object->ID, $value['key'], true ) ); ?></textarea>
            <?
          }
          ?>
        </td>
      </tr>
      <? endforeach; ?>
    </tbody>
  </table>