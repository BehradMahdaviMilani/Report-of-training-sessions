<?php
/*
Plugin Name: گزارش جلسات آموزشی
Plugin URI: لینک به صفحه افزونه در وبسایت شما
Description: این افزونه برای ذخیره و نمایش گزارش‌های جلسات آموزشی به صورت متنی در پنل مدیریت وردپرس است. همچنین، امکان ایجاد گزارشات به فرمت PDF نیز فراهم شده است.
Version: 1.9
Author: Behrad Mahdavi
Author URI: https://ibehrad.ir
License: GPL2
*/

// تابع برای ایجاد دیتابیس در هنگام نصب افزونه
function gzarash_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'session_reports';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        student_name varchar(255) NOT NULL,
        session_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        session_topic varchar(255) NOT NULL,
        session_description text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// فراخوانی تابع برای ایجاد دیتابیس در هنگام نصب افزونه
register_activation_hook(__FILE__, 'gzarash_install');

// تابع برای اضافه کردن منو در پنل مدیریت وردپرس
function gzarash_menu() {
    add_menu_page(
        'گزارش جلسات آموزشی', // عنوان منو
        'گزارش جلسات آموزشی', // عنوان منو در منوی سریع
        'manage_options', // نقش مورد نیاز برای دسترسی به منو
        'gzarash_reports', // شناسه صفحه منو
        'gzarash_reports_page', // تابع برای نمایش صفحه منو
        'dashicons-analytics', // آیکون منو
        30 // موقعیت منو در نوار کناری
    );
}

// فراخوانی تابع برای اضافه کردن منو در هنگام بارگذاری وردپرس
add_action('admin_menu', 'gzarash_menu');

// تابع برای نمایش صفحه گزارشات جلسات آموزشی
function gzarash_reports_page() {
    // اینجا کد HTML و PHP برای نمایش گزارشات جلسات آموزشی قرار می‌گیرد
    global $wpdb;
    $table_name = $wpdb->prefix . 'session_reports';
    $reports = $wpdb->get_results("SELECT * FROM $table_name");
    echo '<div class="wrap"><h2>گزارش جلسات آموزشی</h2>';
    // اضافه کردن لینک به صفحه اضافه کردن گزارش جلسه جدید
    echo '<a href="' . admin_url('admin.php?page=add_new_session_report') . '" class="add-new-h2">افزودن گزارش جلسه جدید</a>';
    if (!empty($reports)) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>نام دانش‌آموز</th><th>تاریخ</th><th>موضوع</th><th>توضیحات</th></tr></thead><tbody>';
        foreach ($reports as $report) {
            echo '<tr><td>' . $report->student_name . '</td><td>' . $report->session_date . '</td><td>' . $report->session_topic . '</td><td>' . $report->session_description . '</td></tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>هیچ گزارشی یافت نشد.</p>';
    }
    echo '</div>';
}

// تابع برای نمایش صفحه اضافه کردن گزارش جلسه جدید
function gzarash_add_new_session_report_page() {
    ?>
    <div class="wrap">
        <h2>افزودن گزارش جلسه آموزشی جدید</h2>
        <form method="post" action="">
            <label for="student_name">نام دانش‌آموز:</label>
            <input type="text" id="student_name" name="student_name" required><br><br>
            <label for="session_date">تاریخ جلسه:</label>
            <input type="date" id="session_date" name="session_date" required><br><br>
            <label for="session_topic">موضوع جلسه:</label>
            <input type="text" id="session_topic" name="session_topic" required><br><br>
            <label for="session_description">توضیحات:</label><br>
            <textarea id="session_description" name="session_description" rows="4" cols="50"></textarea><br><br>
            <input type="submit" name="submit_session" value="ثبت جلسه">
        </form>
    </div>
    <?php
}

// تابع برای ذخیره اطلاعات جلسه آموزشی وقتی کاربر فرم را ارسال می‌کند
function gzarash_add_new_session_report() {
    if (isset($_POST['submit_session'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'session_reports';
        $student_name = $_POST['student_name'];
        $session_date = $_POST['session_date'];
        $session_topic = $_POST['session_topic'];
        $session_description = $_POST['session_description'];
        $wpdb->insert(
            $table_name,
            array(
                'student_name' => $student_name,
                'session_date' => $session_date,
                'session_topic' => $session_topic,
                'session_description' => $session_description
            ),
            array('%s', '%s', '%s', '%s')
        );
        echo '<div class="updated"><p>گزارش جلسه آموزشی با موفقیت ثبت شد!</p></div>';
    }
}

// فراخوانی تابع برای ذخیره اطلاعات جلسه آموزشی
add_action('admin_init', 'gzarash_add_new_session_report');

// تابع برای اضافه کردن منو در پنل مدیریت وردپرس
function gzarash_menu() {
    add_menu_page(
        'گزارش جلسات آموزشی', // عنوان منو
        'گزارش جلسات آموزشی', // عنوان منو در منوی سریع
        'manage_options', // نقش مورد نیاز برای دسترسی به منو
        'gzarash_reports', // شناسه صفحه منو
        'gzarash_reports_page', // تابع برای نمایش صفحه منو
        'dashicons-analytics', // آیکون منو
        30 // موقعیت منو در نوار کناری
    );
}

// فراخوانی تابع برای اضافه کردن منو در هنگام بارگذاری وردپرس
add_action('admin_menu', 'gzarash_menu');

// تابع برای نمایش صفحه گزارشات جلسات آموزشی
function gzarash_reports_page() {
    // اینجا کد HTML و PHP برای نمایش گزارشات جلسات آموزشی قرار می‌گیرد
    global $wpdb;
    $table_name = $wpdb->prefix . 'session_reports';
    $reports = $wpdb->get_results("SELECT * FROM $table_name");
    echo '<div class="wrap"><h2>گزارش جلسات آموزشی</h2>';
    // اضافه کردن لینک به صفحه اضافه کردن گزارش جلسه جدید
    echo '<a href="' . admin_url('admin.php?page=add_new_session_report') . '" class="add-new-h2">افزودن گزارش جلسه جدید</a>';
    if (!empty($reports)) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>نام دانش‌آموز</th><th>تاریخ</th><th>موضوع</th><th>توضیحات</th></tr></thead><tbody>';
        foreach ($reports as $report) {
            echo '<tr><td>' . $report->student_name . '</td><td>' . $report->session_date . '</td><td>' . $report->session_topic . '</td><td>' . $report->session_description . '</td></tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>هیچ گزارشی یافت نشد.</p>';
    }
    echo '</div>';
}

// تابع برای نمایش صفحه اضافه کردن گزارش جلسه جدید
function gzarash_add_new_session_report_page() {
    ?>
    <div class="wrap">
        <h2>افزودن گزارش جلسه آموزشی جدید</h2>
        <form method="post" action="">
            <label for="student_name">نام دانش‌آموز:</label>
            <input type="text" id="student_name" name="student_name" required><br><br>
            <label for="session_date">تاریخ جلسه:</label>
            <input type="date" id="session_date" name="session_date" required><br><br>
            <label for="session_topic">موضوع جلسه:</label>
            <input type="text" id="session_topic" name="session_topic" required><br><br>
            <label for="session_description">توضیحات:</label><br>
            <textarea id="session_description" name="session_description" rows="4" cols="50"></textarea><br><br>
            <input type="submit" name="submit_session" value="ثبت جلسه">
        </form>
    </div>
    <?php
}

// تابع برای ذخیره اطلاعات جلسه آموزشی وقتی کاربر فرم را ارسال می‌کند
function gzarash_add_new_session_report() {
    if (isset($_POST['submit_session'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'session_reports';
        $student_name = $_POST['student_name'];
        $session_date = $_POST['session_date'];
        $session_topic = $_POST['session_topic'];
        $session_description = $_POST['session_description'];
        $wpdb->insert(
            $table_name,
            array(
                'student_name' => $student_name,
                'session_date' => $session_date,
                'session_topic' => $session_topic,
                'session_description' => $session_description
            ),
            array('%s', '%s', '%s', '%s')
        );
        echo '<div class="updated"><p>گزارش جلسه آموزشی با موفقیت ثبت شد!</p></div>';
    }
}

// فراخوانی تابع برای ذخیره اطلاعات جلسه آموزشی
add_action('admin_init', 'gzarash_add_new_session_report');
