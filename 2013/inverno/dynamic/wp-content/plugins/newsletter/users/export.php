<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$options_profile = get_option('newsletter_profile');
$controls = new NewsletterControls();
$module = NewsletterUsers::instance();

$lists = array('0' => 'All');
for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    $lists['' . $i] = '(' . $i . ') ' . $options_profile['list_' . $i];
}
?>

<div class="wrap">
    <?php include NEWSLETTER_DIR . '/users/menu.inc.php'; ?>

    <h2>Export</h2>

    <form method="post" action="<?php echo NEWSLETTER_URL; ?>/users/csv.php">
        <?php $controls->init(); ?>
        <table class="form-table">
            <tr>
                <td>
                    <select name="options[list]" id="options-list">
                        <option value="0">All</option>
                        <option value="1" selected>Only New</option>
                    </select>
                    <?php $controls->button('export', 'Export'); ?>
                </td>
            </tr>
        </table>
    </form>

</div>
