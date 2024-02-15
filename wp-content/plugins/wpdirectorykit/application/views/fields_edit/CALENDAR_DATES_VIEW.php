<?php
/**
 * The template for Edit field CALENDAR DATES VIEW.
 *
 * This is the template that field layout for edit form, readonly
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php

if(isset($field->field))
{
    $field_id = $field->field;
}
else
{
    $field_id = 'field_'.$field->idfield;
}

if(!isset($field->hint))$field->hint = '';
if(!isset($field->columns_number))$field->columns_number = '';
if(!isset($field->class))$field->class = '';

$field_label = $field->field_label;

$required = '';
if(isset($field->is_required) && $field->is_required == 1)
    $required = '*';

?>

<div class="wdk-field-edit <?php echo esc_attr($field->field_type); ?> wdk-col-<?php echo esc_attr($field->columns_number); ?> <?php echo esc_attr($field->class); ?>">
    <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label).esc_html($required); ?></label>
    <div class="wdk-field-container">
        <div class="wdk-field-calendar">
            <div class="hidden data-ajax" data-ajax="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>"></div>
            <div class="hidden js_message_error_date"><?php echo esc_html__('Dates in not available, please set other dates', 'wdk-booking');?></div>
            <div class="wdk-row">
                <?php
                    $available_dates = [];
                    $current_day = date("j");
                    $current_month = date("m");
                    $wdk_order = 0;
                ?>
                <?php for($month_i=0;$month_i < wdk_show_data('month_count', $values, 48, TRUE, TRUE); $month_i++):?>
                    <?php if($month_i%6==0 && $month_i !=0):?>
                        </div>
                        <div class="wdk-row wdk-field-calendar-addinition" style="display: none;">
                    <?php endif;?>
                    <div class="wdk-col">
                        <table>
                        <?php
                        $next_month_time = strtotime("+$month_i month", strtotime(date("F") . "1"));
                        
                        // Get the value of day, month, year
                        $days = array(
                                0 => esc_html__('Sun','wpdirectorykit'),
                                1 => esc_html__('Mon','wpdirectorykit'), 
                                2 => esc_html__('Tue','wpdirectorykit'), 
                                3 => esc_html__('Wed','wpdirectorykit'), 
                                4 => esc_html__('Thu','wpdirectorykit'), 
                                5 => esc_html__('Fri','wpdirectorykit'), 
                                6 => esc_html__('Sat','wpdirectorykit'),
                            );
                        
                        list($mon, $month_m, $month, $year, $num_days) = explode('-', date("n-m-F-Y-t", $next_month_time));

                        $first_day_of_week = array_search(esc_html__(date('D', strtotime($year . '-' . $month . '-1')), 'wpdirectorykit'), $days);
                        if(!$first_day_of_week)
                            $first_day_of_week = 1;

                        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($current_day. '-' . $month . '-' . $year)));
                        $startDay = $first_day_of_week;
                        ?>
                            <caption><?php echo esc_html__($month,'wpdirectorykit');?> <?php echo esc_html($year);?></caption>
                            <thead>
                            <tr>
                                <?php foreach ($days as $key => $day) :?>
                                    <th><?php echo esc_html(substr($day,0,3));?></th>
                                <?php endforeach;?>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php for ($i = $first_day_of_week; $i > 0; $i--):?>
                                        <td class='ignore' data-order="<?php echo esc_attr($wdk_order++);?>"><a><?php echo esc_html(($num_days_last_month-$i+1));?></a></td>
                                    <?php endfor;?>

                                    <?php for ($d=1;$d<=$num_days;$d++):?>
                                        <?php
                                            $date_day = '';
                                            $class_td = '';
                                            if($d<10) {
                                                $date_day = "{$year}-{$month_m}-0{$d}";
                                            } else {
                                                $date_day = "{$year}-{$month_m}-{$d}";
                                            }

                                            if ($d == $current_day && $month_m == $current_month) {
                                                $class_td = "bg-today";
                                            } elseif (isset($available_dates[$date_day])) {
                                                if(strpos($available_dates[$date_day], 'book_book_current') !== FALSE ) {
                                                    $class_td = "bg-booked";
                                                } elseif(strpos($available_dates[$date_day], 'book_current_bookd') !== FALSE ) {
                                                    $class_td = "bg-booked";
                                                } elseif(strpos($available_dates[$date_day], 'reservation_start') !== FALSE && strpos($available_dates[$date_day], 'reservation_end') === FALSE) {
                                                    $class_td = "bg-available bg-booked reservation-start";
                                                } elseif(strpos($available_dates[$date_day], 'reservation_end') !== FALSE && strpos($available_dates[$date_day], 'reservation_start') === FALSE) {
                                                    $class_td = "bg-available bg-booked reservation-end";
                                                } elseif(strpos($available_dates[$date_day], 'booked') !== FALSE) {
                                                    $class_td = "bg-booked";
                                                } else {
                                                    $class_td = "bg-available";
                                                }
                                            } else {
                                                $class_td = "bg-not-selected";
                                            }
                                            $startDay++;
                                        ?>
                                        <td data-order="<?php echo esc_attr($wdk_order++);?>" class="<?php echo esc_html($class_td);?>"><a title="<?php echo esc_html($date_day);?>"><?php echo esc_html($d);?></a></td>

                                        <?php if($startDay > 6 && $d < $num_days):?>
                                            <?php $startDay = 0; ?>
                                            </tr><tr>
                                        <?php endif;?>
                                    <?php endfor;?>

                                    <?php for ($i = $startDay,$y=1; $i <= 6 ; $i++,$y++):?>
                                        <td class='ignore'><a><?php echo esc_html($y);?></a></td>
                                    <?php endfor;?>
                                </tr>
                            </tbody>
                        </table>
                    </div> 
                <?php endfor;?>
            </div> 
            <div class="wdk-row">
                <div class="wdk-col wdk-col-full wdk-cal-pag">
                    <a href="#" class="wdk-btn-pag next">
                        <span class="dashicons dashicons-plus-alt2"></span>
                    </a>
                    <a href="#" class="wdk-btn-pag pre noactive">
                        <span class="dashicons dashicons-minus"></span>
                    </a>
                </div>
            </div>
        </div>
        <?php if(!empty($field->hint)):?>
        <p class="wdk-hint">
            <?php echo esc_html($field->hint); ?>
        </p>
        <?php endif;?>
    </div>
</div>