<?php

# Version 1.0.0
#
# General
$lang['id'] = 'ID';
$lang['name'] = 'Name';
$lang['options'] = 'Options';
$lang['submit'] = 'Save';
$lang['added_successfuly'] = '%s added successfuly.';
$lang['updated_successfuly'] = '%s updated successfuly.';
$lang['edit'] = 'Edit %s';
$lang['add_new'] = 'Add new %s';
$lang['deleted'] = '%s deleted';
$lang['problem_deleting'] = 'Problem deleting %s';
$lang['is_referenced'] = 'The ID of the %s is already using.';
$lang['close'] = 'Close';
$lang['send'] = 'Send';
$lang['cancel'] = 'Cancel';
$lang['go_back'] = 'Go Back';
$lang['error_uploading_file'] = 'Error uploading file';
$lang['load_more'] = 'Load More';
$lang['cant_delete_default'] = 'Cant delete default %s';

# Invoice General
$lang['invoice_status_paid'] = 'Paid';
$lang['invoice_status_unpaid'] = 'Unpaid';
$lang['invoice_status_overdue'] = 'Overdue';
$lang['invoice_status_not_paid_completely'] = 'Partially Paid';

$lang['invoice_pdf_heading'] = 'INVOICE';

$lang['invoice_table_item_heading'] = 'Item';
$lang['invoice_table_quantity_heading'] = 'Qty';
$lang['invoice_table_rate_heading'] = 'Rate';
$lang['invoice_table_tax_heading'] = 'Tax';
$lang['invoice_table_amount_heading'] = 'Amount';
$lang['invoice_subtotal'] = 'Sub Total';
$lang['invoice_adjustment'] = 'Adjustment';
$lang['invoice_total'] = 'Total';
$lang['invoice_vat'] = 'VAT Number';
$lang['invoice_bill_to'] = 'Bill To';
$lang['invoice_data_date'] = 'Invoice Date:';
$lang['invoice_data_duedate'] = 'Due Date:';
$lang['invoice_received_payments'] = 'Transactions';
$lang['invoice_no_payments_found'] = 'No Payments found for this invoice';
$lang['invoice_note'] = 'Note:';
$lang['invoice_payments_table_number_heading'] = 'Payment #';
$lang['invoice_payments_table_mode_heading'] = 'Payment Mode';
$lang['invoice_payments_table_date_heading'] = 'Date';
$lang['invoice_payments_table_amount_heading'] = 'Amount';


# Announcements
$lang['announcement'] = 'Announcement';
$lang['announcement_lowercase'] = 'announcement';
$lang['announcements'] = 'Announcements';
$lang['announcements_lowercase'] = 'announcements';
$lang['new_announcement'] = 'New Announcement';
$lang['announcement_name'] = 'Announcement name';
$lang['announcement_message'] = 'Message';
$lang['announcement_show_to_staff'] = 'Show to staff';
$lang['announcement_show_to_clients'] = 'Show to clients';
$lang['announcement_show_my_name'] = 'Show my name';

# Affiliates
$lang['affiliates'] = 'Affiliates';
$lang['affiliate'] = 'Affiliate';
$lang['new_affiliate'] = 'New Affiliate';
$lang['affiliate_lowercase'] = 'affiliate';
$lang['affiliate_delete_tooltip'] = 'All Affiliate data will be deleted.';

$lang['affiliates_list_full_name'] = 'Full Name';
$lang['affiliates_list_email'] = 'Email';
$lang['affiliates_list_phone'] = 'Phone';
$lang['affiliates_list_last_login'] = 'Last Login';
$lang['affiliates_list_last_active'] = 'Active';

$lang['affiliates_summary'] = 'Affiliates Summary';
$lang['affiliates_summary_active'] = 'Active';
$lang['affiliates_summary_inactive'] = 'Inactive';
$lang['affiliates_summary_companies'] = 'Companies';
$lang['affiliates_summary_individual'] = 'Individual';
$lang['affiliates_summary_logged_in_today'] = 'Logged In Today';

$lang['affiliate_view_by'] = 'View Affiliates';

# Affiliate
$lang['set_password_email_sent_to_affiliate'] = 'Email to set password is successfuly sent to the affiliate';
$lang['set_password_email_sent_to_affiliate_and_profile_updated'] = 'Profile updated and email to set password is successfuly sent to the affiliate';

$lang['affiliate_firstname'] = 'First Name';
$lang['affiliate_lastname'] = 'Last Name';
$lang['affiliate_email'] = 'Email';
$lang['affiliate_company'] = 'Company';
$lang['affiliate_vat_number'] = 'VAT Number';
$lang['affiliate_address'] = 'Address';
$lang['affiliate_city'] = 'City';
$lang['affiliate_postal_code'] = 'Postal Code';
$lang['affiliate_state'] = 'State';
$lang['affiliate_phonenumber'] = 'Phone number';
$lang['affiliate_password'] = 'Password';
$lang['affiliate_password_change_populate_note'] = 'Note: if you populate this fields, password will be changed on this customer.';
$lang['affiliate_password_last_changed'] = 'Password last changed:';
$lang['login_as_affiliate'] = 'Login as affiliate';

$lang['affiliate_do_not_send_welcome_email'] = 'Do not send welcome email';
$lang['affiliate_send_set_password_email'] = 'Send SET password email';

# Clients
$lang['clients'] = 'Customers';
$lang['client'] = 'Customer';
$lang['new_client'] = 'New Customer';
$lang['client_lowercase'] = 'customer';
$lang['client_delete_tooltip'] = 'All customer data will be deleted. Contracts,tickets,notes. NOTE: If invoices found customer wont be deleted. You need to assign this invoices to another customer to keep the invoice number';
$lang['customer_delete_invoices_warning'] = 'This customer have invoices on the account. You cant delete this customer. Change all invoices to another customer in a future then delete.';
$lang['client_firstname'] = 'First Name';
$lang['client_lastname'] = 'Last Name';
$lang['client_email'] = 'Email';
$lang['client_company'] = 'Company';
$lang['client_vat_number'] = 'VAT Number';
$lang['client_address'] = 'Address';
$lang['client_city'] = 'City';
$lang['client_postal_code'] = 'Postal Code';
$lang['client_state'] = 'State';
$lang['client_password'] = 'Password';
$lang['client_password_change_populate_note'] = 'Note: if you populate this fields, password will be changed on this customer.';
$lang['client_password_last_changed'] = 'Password last changed:';
$lang['login_as_client'] = 'Login as client';
$lang['client_invoices_tab'] = 'Invoices';
$lang['contracts_invoices_tab'] = 'Contracts';
$lang['contracts_tickets_tab'] = 'Tickets';
$lang['contracts_notes_tab'] = 'Notes';
$lang['client_invoice_number_table_heading'] = 'Invoice #';
$lang['client_invoice_date_table_heading'] = 'Date';
$lang['client_invoice_due_date_table_heading'] = 'Due Date';
$lang['client_string_table_heading'] = 'Client';
$lang['client_amount_table_heading'] = 'Amount';
$lang['client_status_table_heading'] = 'Status';
$lang['note_description'] = 'Note description';
$lang['no_user_notes'] = 'No user notes found';

$lang['client_string_contracts_table_heading'] = 'Client';
$lang['client_start_date_contracts_table_heading'] = 'Start Date';
$lang['client_end_date_contracts_table_heading'] = 'End Date';
$lang['client_description_contracts_table_heading'] = 'Description';
$lang['client_do_not_send_welcome_email'] = 'Do not send welcome email';

$lang['clients_notes_table_description_heading'] = 'Description';
$lang['clients_notes_table_addedfrom_heading'] = 'Added From';
$lang['clients_notes_table_dateadded_heading'] = 'Date Added';

$lang['clients_list_full_name'] = 'Full Name';
$lang['clients_list_email'] = 'Email';
$lang['clients_list_last_login'] = 'Last Login';
$lang['clients_list_last_active'] = 'Active';

# Contracts
$lang['contracts'] = 'Contracts';
$lang['contract'] = 'Contract';
$lang['new_contract'] = 'New Contract';
$lang['contract_lowercase'] = 'contract';
$lang['contract_start_date'] = 'Start Date';
$lang['contract_end_date'] = 'End Date';
$lang['contract_subject'] = 'Subject';
$lang['contract_description'] = 'Description';
$lang['contract_subject_tooltip'] = 'Subject is also visible to customer';
$lang['contract_client_string'] = 'Client';
$lang['contract_attach'] = 'Attach document';

$lang['contract_list_client'] = 'Client';
$lang['contract_list_subject'] = 'Subject';
$lang['contract_list_start_date'] = 'Start Date';
$lang['contract_list_end_date'] = 'End Date';

# Currencies
$lang['currencies'] = 'Currencies';
$lang['currency'] = 'Currency';
$lang['new_currency'] = 'New Currency';
$lang['currency_lowercase'] = 'currency';
$lang['base_currency_set'] = 'This is now your base currency.';
$lang['make_base_currency'] = 'Make base currency';
$lang['base_currency_string'] = 'Base Currency';

$lang['currency_list_name'] = 'Name';
$lang['currency_list_symbol'] = 'Symbol';


$lang['currency_add_edit_description'] = 'Currency Name';
$lang['currency_add_edit_rate'] = 'Symbol';

$lang['currency_edit_heading'] = 'Edit Currency';
$lang['currency_add_heading'] = 'Add New Currency';


# Department
$lang['departments'] = 'Departments';
$lang['department'] = 'Department';
$lang['new_department'] = 'New Department';
$lang['department_lowercase'] = 'department';

$lang['department_name'] = 'Department Name';
$lang['department_email'] = 'Department Email';
$lang['department_hide_from_client'] = 'Hide from client?';
$lang['department_list_name'] = 'Name';

# Email Templates
$lang['email_templates'] = 'Email Templates';
$lang['email_template'] = 'Email Template';
$lang['email_template_lowercase'] = 'email template';
$lang['email_templates_lowercase'] = 'email templates';
$lang['email_template_ticket_fields_heading'] = 'Tickets';
$lang['email_template_invoices_fields_heading'] = 'Invoices';
$lang['email_template_clients_fields_heading'] = 'Customers';

$lang['template_name'] = 'Template Name';
$lang['template_subject'] = 'Subject';
$lang['template_fromname'] = 'From Name';
$lang['template_fromemail'] = 'From Email';
$lang['send_as_plain_text'] = 'Send as Plaintext';
$lang['email_template_disabed'] = 'Disabled';
$lang['email_template_email_message'] = 'Email message';
$lang['email_template_merge_fields'] = 'Merge fields';
$lang['available_merge_fields'] = 'Available merge fields';
# Home
$lang['dashboard_string'] = 'Dashboard';
$lang['home_latest_todos'] = 'Latest todo\'s';
$lang['home_no_latest_todos'] = 'No todos found';
$lang['home_latest_finished_todos'] = 'Latest finished todo\'s';
$lang['home_no_finished_todos_found'] = 'No finished todos found';
$lang['home_todo_heading'] = 'To do items';
$lang['home_tickets_awaiting_reply_by_department'] = 'Tickets awaiting reply by department';
$lang['home_tickets_awaiting_reply_by_status'] = 'Tickets awaiting reply by status';
$lang['home_this_week_events'] = 'This week events';
$lang['home_upcoming_events_next_week'] = 'Upcoming events next week';
$lang['home_event_added_by'] = 'Event added by';
$lang['home_public_event'] = 'Public event';
$lang['home_weekly_payment_records'] = 'Weekly Payment Records';
$lang['home_weekend_ticket_opening_statistics'] = 'Weekly Ticket Openings Statistics';
# Newsfeed
$lang['whats_on_your_mind'] = 'Whats on your mind?';
$lang['new_post'] = 'Post';
$lang['newsfeed_upload_tooltip'] = 'Tip:Drag and drop files to upload';
$lang['newsfeed_all_departments'] = 'All Departments';
$lang['newsfeed_pin_post'] = 'Pin post';
$lang['newsfeed_unpin_post'] = 'Unpin post';
$lang['newsfeed_delete_post'] = 'Delete';
$lang['newsfeed_published_post'] = 'Published';
$lang['newsfeed_you_like_this'] = 'You like this';
$lang['newsfeed_like_this'] = 'like this';
$lang['newsfeed_one_other'] = 'other';
$lang['newsfeed_you'] = 'You';
$lang['newsfeed_and'] = 'and';
$lang['newsfeed_you_and'] = 'You and';
$lang['newsfeed_like_this_saying'] = 'Like this';
$lang['newsfeed_unlike_this_saying'] = 'Unlike this';
$lang['newsfeed_show_more_comments'] = 'Show more comments';
$lang['comment_this_post_placeholder'] = 'Comment this post..';
$lang['newsfeed_post_likes_modal_heading'] = 'Coleques who like this post';
$lang['newsfeed_comment_likes_modal_heading'] = 'Coleques who like this comment';
$lang['newsfeed_newsfeed_post_only_visible_to_departments'] = 'This post is only visible to the following departments: %s';
# Invoice Items
$lang['invoice_items'] = 'Invoice Items';
$lang['invoice_item'] = 'Invoice Item';
$lang['new_invoice_item'] = 'New Item';
$lang['invoice_item_lowercase'] = 'invoice item';

$lang['invoice_items_list_description'] = 'Description';
$lang['invoice_items_list_rate'] = 'Rate';
$lang['invoice_items_list_tax'] = 'Tax';

$lang['invoice_item_add_edit_description'] = 'Description';
$lang['invoice_item_add_edit_rate'] = 'Rate';
$lang['invoice_item_add_edit_tax'] = 'Tax';
$lang['invoice_item_add_edit_tax_select'] = 'Select Tax';

$lang['invoice_item_edit_heading'] = 'Edit Item';
$lang['invoice_item_add_heading'] = 'Add New Item';

# Invoices


$lang['invoices'] = 'Invoices';
$lang['invoice'] = 'Invoice';
$lang['invoice_lowercase'] = 'invoice';
$lang['create_new_invoice'] = 'Create New Invoice';
$lang['view_invoice'] = 'View Invoice';
$lang['invoice_number_changed'] = 'Invoice created successfuly but the number is changed becuase someone added new invoice before you.';
$lang['invoice_payment_recorded'] = 'Invoice Payment Recorded';
$lang['invoice_payment_record_failed'] = 'Failed to Record Invoice Payment';
$lang['invoice_sent_to_client_success'] = 'The invoice is sent successfuly to the client';
$lang['invoice_sent_to_client_fail'] = 'Problem while sending the invoice';
$lang['invoice_reminder_send_problem'] = 'Problem sending invoice overdue reminder';
$lang['invoice_overdue_reminder_sent'] = 'Invoice Overdue Reminder Successfuly Sent';

$lang['invoice_details'] = 'Invoice Details';
$lang['invoice_view'] = 'View Invoice';
$lang['invoice_select_customer'] = 'Customer';
$lang['invoice_add_edit_number'] = 'Invoice Number';
$lang['invoice_add_edit_date'] = 'Invoice Date';
$lang['invoice_add_edit_duedate'] = 'Due Date';
$lang['invoice_add_edit_currency'] = 'Currency';
$lang['invoice_add_edit_client_note'] = 'Client Note';
$lang['invoice_add_edit_admin_note'] = 'Admin Note';

$lang['invoice_add_edit_search_item'] = 'Search Items';
$lang['invoices_toggle_table_tooltip'] = 'View Full Table';





$lang['edit_invoice_tooltip'] = 'Edit Invoice';
$lang['delete_invoice_tooltip'] = 'Delete Invoice. Note: All payments regarding to this invoice will be deleted (if any).';
$lang['invoice_sent_to_email_tooltip'] = 'Send to Email';
$lang['invoice_already_send_to_client_tooltip'] = 'This invoice is already sent to the client %s';
$lang['send_overdue_notice_tooltip'] = 'Send Overdue Notice';
$lang['invoice_view_activity_tooltip'] = 'Activity Log';
$lang['invoice_record_payment'] = 'Record Payment';


$lang['invoice_send_to_client_modal_heading'] = 'Send this invoice to client';
$lang['invoice_send_to_client_attach_pdf'] = 'Attach Invoice PDF';
$lang['invoice_send_to_client_preview_template'] = 'Preview Email Template';

$lang['invoice_dt_table_heading_number'] = 'Invoice #';
$lang['invoice_dt_table_heading_date'] = 'Date';
$lang['invoice_dt_table_heading_client'] = 'Client';
$lang['invoice_dt_table_heading_duedate'] = 'Due Date';
$lang['invoice_dt_table_heading_amount'] = 'Amount';
$lang['invoice_dt_table_heading_status'] = 'Status';

$lang['record_payment_for_invoice'] = 'Record Payment for';
$lang['record_payment_amount_received'] = 'Amount Received';
$lang['record_payment_date'] = 'Payment Date';
$lang['record_payment_leave_note'] = 'Leave a note';
$lang['invoice_payments_received'] = 'Payments Received';
$lang['invoice_record_payment_note_placeholder'] = 'Admin Note';
$lang['no_payments_found'] = 'No Payments found for this invoice';
$lang['invoice_email_link_text'] = 'View Invoice';

# Payments
$lang['payments'] = 'Payments';
$lang['payment'] = 'Payment';
$lang['payment_lowercase'] = 'payment';
$lang['payments_table_number_heading'] = 'Payment #';
$lang['payments_table_invoicenumber_heading'] = 'Invoice #';
$lang['payments_table_mode_heading'] = 'Payment Mode';
$lang['payments_table_date_heading'] = 'Date';
$lang['payments_table_amount_heading'] = 'Amount';
$lang['payments_table_client_heading'] = 'Client';
$lang['payment_not_exists'] = 'The payment does not exists';

$lang['payment_edit_for_invoice'] = 'Payment for Invoice';
$lang['payment_edit_amount_received'] = 'Amount Received';
$lang['payment_edit_date'] = 'Payment Date';
$lang['payment_edit_lave_note'] = 'Leave Note';


# Knowledge Base
$lang['kb_article_add_edit_subject'] = 'Subject';
$lang['kb_article_add_edit_group'] = 'Group';
$lang['kb_string'] = 'Knowledge Base';
$lang['kb_article'] = 'Article';
$lang['kb_article_lowercase'] = 'article';
$lang['kb_article_new_article'] = 'New Article';
$lang['kb_article_disabled'] = 'Disabled';
$lang['kb_article_description'] = 'Article description';

$lang['kb_table'] = 'List';
$lang['kb_no_articles_found'] = 'No knowledgbase articles found';
$lang['kb_dt_article_name'] = 'Article Name';
$lang['kb_dt_group_name'] = 'Group';
$lang['new_group'] = 'New Group';
$lang['kb_group_add_edit_name'] = 'Group Name';
$lang['kb_group_add_edit_description'] = 'Short description';
$lang['kb_group_add_edit_disabled'] = 'Disabled';
$lang['kb_group_add_edit_note'] = 'Note: All articles in this group will be hidden if disabled is checked';
$lang['group_table_name_heading'] = 'Name';
$lang['group_table_isactive_heading'] = 'Active';
$lang['kb_no_groups_found'] = 'No knowledge base groups found';

# Mail Lists
$lang['mail_lists'] = 'Mail Lists';
$lang['mail_list'] = 'Mail List';
$lang['new_mail_list'] = 'New Mail List';
$lang['mail_list_lowercase'] = 'mail list';
$lang['custom_field_deleted_success'] = 'Custom field deleted';
$lang['custom_field_deleted_fail'] = 'Problem deleting custom field';
$lang['email_removed_from_list'] = 'Email removed from list';
$lang['email_remove_fail'] = 'Email removed from list';
$lang['staff_mail_lists'] = 'Staff Mail List';
$lang['clients_mail_lists'] = 'Clients Mail List';
$lang['mail_list_total_imported'] = 'Total emails imported: %s';
$lang['mail_list_total_duplicate'] = 'Total duplicate emails: %s';
$lang['mail_list_total_failed_to_insert'] = 'Emails failed to insert: %s';
$lang['mail_list_total_invalid'] = 'Invalid email address: %s';
$lang['cant_edit_mail_list'] = 'You cant edit this list, this list is populated automatically';
$lang['mail_list_add_edit_name'] = 'Mail List Name';
$lang['mail_list_add_edit_customfield'] = 'Add custom field';
$lang['mail_lists_viewing_emails'] = 'Viewing Emails From List';
$lang['mail_lists_view_email_email_heading'] = 'Email';
$lang['mail_lists_view_email_date_heading'] = 'Date Added';
$lang['add_new_email_to'] = 'Add New Email to %s';
$lang['import_emails_to'] = 'Import Emails to %s';
$lang['mail_list_new_email_edit_add_label'] = 'Email';
$lang['mail_list_import_file'] = 'Import File';
$lang['mail_list_available_custom_fields'] = 'Available Custom Fields';
$lang['submit_import_emails'] = 'Import Emails';
$lang['btn_import_emails'] = 'Import Emails (Excel)';
$lang['btn_add_email_to_list'] = 'Add Email to This List';
$lang['mail_lists_dt_list_name'] = 'List Name';
$lang['mail_lists_dt_datecreated'] = 'Date Created';
$lang['mail_lists_dt_creator'] = 'Creator';
$lang['email_added_to_mail_list_successfuly'] = 'Email added to list';
$lang['email_is_duplicate_mail_list'] = 'Email already exists in this list';

# Media
$lang['media_dt_filename'] = 'Filename';
$lang['media_dt_last_modified'] = 'Last Modified';
$lang['media_dt_filesize'] = 'File Size';
$lang['media_dt_mime_type'] = 'Type';
$lang['media_heading'] = 'Media';
$lang['media_files'] = 'Files';

# Payment modes
$lang['new_payment_mode'] = 'New Payment Mode';
$lang['payment_modes'] = 'Payment Modes';
$lang['payment_mode'] = 'Payment Mode';
$lang['payment_mode_lowercase'] = 'payment mode';
$lang['payment_modes_dt_name'] = 'Payment Mode Name';

$lang['payment_mode_add_edit_name'] = 'Payment Mode Name';
$lang['payment_mode_edit_heading'] = 'Edit Payment Mode';
$lang['payment_mode_add_heading'] = 'Add New Payment Mode';

# Predefined Ticket Replies
$lang['new_predefined_reply'] = 'New Predefined Reply';
$lang['predefined_replies'] = 'Predefined Replise';
$lang['predefined_reply'] = 'Predefined Reply';
$lang['predefined_reply_lowercase'] = 'predefined peply';
$lang['predifined_replies_dt_name'] = 'Predifined Reply Name';
$lang['predifined_reply_add_edit_name'] = 'Predifined Reply Name';
$lang['predifined_reply_add_edit_content'] = 'Reply Content';

# Ticket Priorities
$lang['new_ticket_priority'] = 'New Priority';
$lang['ticket_priorities'] = 'Ticket Priorities';
$lang['ticket_priority'] = 'Ticket Priority';
$lang['ticket_priority_lowercase'] = 'ticket priority';
$lang['no_ticket_priorities_found'] = 'No Ticket Priorities Found';
$lang['ticket_priority_dt_name'] = 'Ticket Priority Name';
$lang['ticket_priority_add_edit_name'] = 'Priority Name';

# Reports
$lang['kb_reports'] = 'Knowledge base articles reports';
$lang['sales_reports'] = 'Sales Reports';
$lang['reports_choose_kb_group'] = 'Choose Group';
$lang['reports_sales_select_report_type'] = 'Select Report Type';
$lang['report_kb_yes'] = 'Yes';
$lang['report_kb_no'] = 'No';
$lang['report_kb_no_votes'] = 'No votes yet';
$lang['report_this_week_leads_conversions'] = 'This Week Leads Conversions';
$lang['report_leads_sources_conversions'] = 'Sources';
$lang['report_leads_monthly_conversions'] = 'Monthly';
$lang['sales_report_heading'] = 'Sales Report';
$lang['report_sales_type_income'] = 'Total Income';

$lang['report_sales_type_customer'] = 'Customer Report';
$lang['report_sales_base_currency_select_explanation'] = 'You need to select currency becuase you have invoices with different currency';
$lang['report_sales_from_date'] = 'From Date';
$lang['report_sales_to_date'] = 'To Date';


$lang['report_sales_months_all_time'] = 'All Time';
$lang['report_sales_months_six_months'] = 'Last 6 months';
$lang['report_sales_months_twelve_months'] = 'Last 12 months';
$lang['report_sales_months_custom'] = 'Custom';
$lang['reports_sales_generated_report'] = 'Generated Report';



$lang['reports_sales_dt_customers_client'] = 'Client';
$lang['reports_sales_dt_customers_total_invoices'] = 'Total Invoices';
$lang['reports_sales_dt_items_customers_amount'] = 'Amount';
$lang['reports_sales_dt_items_customers_amount_with_tax'] = 'Amount with Tax';

# Roles
$lang['new_role'] = 'New Role';
$lang['all_roles'] = 'All Role';
$lang['roles'] = 'Staff Roles';
$lang['role'] = 'Role';
$lang['role_lowercase'] = 'role';
$lang['roles_total_users'] = 'Total Users: ';
$lang['roles_dt_name'] = 'Role Name';
$lang['role_add_edit_name'] = 'Role Name';

# Service
$lang['new_service'] = 'New Service';
$lang['services'] = 'Services';
$lang['service'] = 'Service';
$lang['service_lowercase'] = 'service';
$lang['services_dt_name'] = 'Service Name';
$lang['service_add_edit_name'] = 'Service Name';

# Settings
$lang['settings'] = 'Settings';
$lang['settings_updated'] = 'Settings Updated';
$lang['settings_save'] = 'Save Settings';
$lang['settings_group_general'] = 'General';
$lang['settings_group_localization'] = 'Localization';
$lang['settings_group_tickets'] = 'Tickets';
$lang['settings_group_sales'] = 'Finance';
$lang['settings_group_email'] = 'Email';
$lang['settings_group_clients'] = 'Clients';
$lang['settings_group_newsfeed'] = 'Newsfeed';
$lang['settings_group_cronjob'] = 'Cron Job';
$lang['settings_group_notifications'] = 'Notifications';

$lang['settings_yes'] = 'Yes';
$lang['settings_no'] = 'No';
$lang['settings_clients_default_theme'] = 'Default clients theme';
$lang['settings_clients_allow_registration'] = 'Allow clients to register';
$lang['settings_clients_allow_kb_view_without_registration'] = 'Allow knowledge base to be viewed without registration';

$lang['settings_cron_send_overdue_reminder'] = 'Send invoice overdue reminder';
$lang['settings_cron_send_overdue_reminder_tooltip'] = 'Send overdue email to client when invoice status updated to overdue from Cron Job';
$lang['automatically_send_invoice_overdue_reminder_after'] = 'Automatically send reminder after (days)';
$lang['automatically_resend_invoice_overdue_reminder_after'] = 'Automatically re-send reminder after (days)';

$lang['settings_email_host'] = 'SMTP Host';
$lang['settings_email_port'] = 'SMTP Port';
$lang['settings_email'] = 'SMTP Email';
$lang['settings_email_password'] = 'SMTP Password';
$lang['settings_email_charset'] = 'Email Charset';
$lang['settings_email_signature'] = 'Email Signature';

$lang['settings_general_company_logo'] = 'Company Logo';
$lang['settings_general_company_logo_tooltip'] = 'Recomended dimensions: 150 x 32px';
$lang['settings_general_company_remove_logo_tooltip'] = 'Remove company logo';
$lang['settings_general_company_name'] = 'Company Name';
$lang['settings_general_company_main_domain'] = 'Company Main Domain';
$lang['settings_general_use_knowledgebase'] = 'Use Knowledge Base';
$lang['settings_general_use_knowledgebase_tooltip'] = 'If you allow this options knowledge base will be shown also on clients side';
$lang['settings_general_tables_limit'] = 'Tables Pagination Limit';
$lang['settings_general_default_staff_role'] = 'Default Staff Role';
$lang['settings_general_default_staff_role_tooltip'] = 'When you add new staff member this role will be selected by default';

$lang['settings_localization_date_format'] = 'Date Format';
$lang['settings_localization_default_timezone'] = 'Default Timezone';
$lang['settings_localization_default_language'] = 'Default Language';

$lang['settings_newsfeed_allowed_file_extensions'] = 'Allowed file extensions for uploads';
$lang['settings_newsfeed_max_file_upload_post'] = 'Maximum files upload on post';
$lang['settings_newsfeed_max_file_size'] = 'Maximum files size (MB)';

$lang['settings_reminders_contracts'] = 'Contract expiration reminder';
$lang['settings_reminders_contracts_tooltip'] = 'Expiration reminder notification in days';

$lang['settings_tickets_use_services'] = 'Use Services';
$lang['settings_tickets_max_attachments'] = 'Maximum ticket attachments';
$lang['settings_tickets_allow_departments_access'] = 'Allow staff to access only ticket that belongs to staff departments';
$lang['settings_tickets_allowed_file_extensions'] = 'Allowed attachments file extensions';

$lang['settings_sales_general'] = 'General';
$lang['settings_sales_general_note'] = 'General settings';
$lang['settings_sales_invoice_prefix'] = 'Invoice Number Prefix';
$lang['settings_sales_decimal_separator'] = 'Decimal Separator';
$lang['settings_sales_thousand_separator'] = 'Thousand Separator';
$lang['settings_sales_currency_placement'] = 'Currency Placement';
$lang['settings_sales_currency_placement_before'] = 'Before Amount';
$lang['settings_sales_currency_placement_after'] = 'After Amount';
$lang['settings_sales_require_client_logged_in_to_view_invoice'] = 'Require client to be logged in to view invoice';
$lang['settings_sales_next_invoice_number'] = 'Next Invoice Number';
$lang['settings_sales_next_invoice_number_tooltip'] = 'Set this field to 1 if you want to start from begining';
$lang['settings_sales_decrement_invoice_number_on_delete'] = 'Decrement invoice number on delete';
$lang['settings_sales_decrement_invoice_number_on_delete_tooltip'] = 'Do you want to decrement the invoice number when the last invoice is deleted? Ex. If is set this option to YES and before invoice delete the next invoice number is 15 the next invoice number will decrement to 14 for the next invoice if is set to NO the number will remain to 15';
$lang['settings_sales_invoice_number_format'] = 'Invoice Number Format';
$lang['settings_sales_invoice_number_format_year_based'] = 'Year Based';
$lang['settings_sales_invoice_number_format_number_based'] = 'Number Based (000001)';
$lang['settings_sales_invoice_year'] = 'Invoice Year (YYYY/000001)';
$lang['settings_sales_invoice_year_tooltip'] = 'Current invoice year. Reset this when new year arrives.';

$lang['settings_sales_company_info_heading'] = 'Company';
$lang['settings_sales_company_info_note'] = 'This informations will be displayed on invoices/estimates/payments and other PDF documents where company info is required';
$lang['settings_sales_company_name'] = 'Company Name';
$lang['settings_sales_address'] = 'Address';
$lang['settings_sales_city'] = 'City';
$lang['settings_sales_country_code'] = 'Country Code';
$lang['settings_sales_postal_code'] = 'Postal Code';
$lang['settings_sales_phonenumber'] = 'Phone';

# Leads
$lang['new_lead'] = 'New Lead';
$lang['leads'] = 'Leads';
$lang['lead'] = 'Lead';
$lang['lead_lowercase'] = 'Lead';
$lang['leads_all'] = 'All';

$lang['leads_canban_notes'] = 'Notes: %s';
$lang['leads_canban_source'] = 'Source: %s';

$lang['lead_new_source'] = 'New Source';
$lang['lead_sources'] = 'Lead Sources';
$lang['lead_source'] = 'Lead Source';
$lang['lead_source_lowercase'] = 'lead source';
$lang['leads_sources_not_found'] = 'No leads sources found';
$lang['leads_sources_table_name'] = 'Source Name';
$lang['leads_source_add_edit_name'] = 'Source Name';

$lang['lead_new_status'] = 'New Lead Status';
$lang['lead_statuss'] = 'Lead Status';
$lang['lead_status'] = 'Lead Status';
$lang['lead_status_lowercase'] = 'lead status';
$lang['leads_status_table_name'] = 'Status Name';

$lang['leads_status_add_edit_name'] = 'Status Name';
$lang['leads_status_add_edit_order'] = 'Order';

$lang['lead_statuses_not_found'] = 'No leads statuses found';
$lang['lead_noted_added_successfuly'] = 'Lead note added succesfuly';
$lang['lead_status_updated'] = 'Lead updated';
$lang['leads_search'] = 'Search Leads';

$lang['leads_table_total'] = 'Total Leads: %s';

$lang['leads_dt_name'] = 'Name';
$lang['leads_dt_email'] = 'Email';
$lang['leads_dt_phonenumber'] = 'Phone';
$lang['leads_dt_assigned'] = 'Assigned';
$lang['leads_dt_status'] = 'Status';
$lang['leads_dt_last_contact'] = 'Last Contact';

$lang['lead_add_edit_name'] = 'Name';
$lang['lead_add_edit_email'] = 'Email Address';
$lang['lead_add_edit_phonenumber'] = 'Phone';
$lang['lead_add_edit_source'] = 'Source';
$lang['lead_add_edit_status'] = 'Lead Status';
$lang['lead_add_edit_assigned'] = 'Assigned';
$lang['lead_add_edit_datecontacted'] = 'Date Contacted';
$lang['lead_add_edit_contected_today'] = 'Contacted Today';
$lang['lead_add_edit_activity'] = 'Activity Log';
$lang['lead_add_edit_notes'] = 'Notes';
$lang['lead_add_edit_add_note'] = 'Add note';
$lang['lead_not_contacted'] = 'I have not contacted this lead';
$lang['lead_add_edit_contected_this_lead'] = 'I got in touch with this lead';
$lang['lead_confirmation_canban_contacted'] = 'Have you got in touch with this lead?';

# Misc
$lang['activity_log_when_cron_job'] = 'Cron Job';
$lang['access_denied'] = 'Acccess denied';
$lang['prev'] = 'Prev';
$lang['next'] = 'next';

# Datatables
$lang['dt_paginate_first'] = 'First';
$lang['dt_paginate_last'] = 'Last';
$lang['dt_paginate_next'] = 'Next';
$lang['dt_paginate_previous'] = 'Previous';
$lang['dt_empty_table'] = 'No {0} found';
$lang['dt_search'] = 'Search:';
$lang['dt_zero_records'] = 'No matching records found';
$lang['dt_loading_records'] = 'Loading...';
$lang['dt_length_menu'] = 'Show _MENU_ ';
$lang['dt_info_filtered'] = '(filtered from _MAX_ total {0})';
$lang['dt_info_empty'] = 'Showing 0 to 0 of 0 {0}';
$lang['dt_info'] = 'Showing _START_ to _END_ of _TOTAL_ {0}';
$lang['dt_empty_table'] = 'No {0} found';
$lang['dt_sort_ascending'] = 'activate to sort column ascending';
$lang['dt_sort_descending'] = 'activate to sort column descending';
# Invoice Activity Log
$lang['user_sent_overdue_reminder'] = '%s sent invoice overdue reminder';

# Weekdays
$lang['wd_monday'] = 'Monday';
$lang['wd_tuesday'] = 'Tuesday';
$lang['wd_thursday'] = 'Thursday';
$lang['wd_wednesday'] = 'Wednesday';
$lang['wd_friday'] = 'Friday';
$lang['wd_saturday'] = 'Saturday';
$lang['wd_sunday'] = 'Sunday';

# Admin Left Sidebar
$lang['als_dashboard'] = 'Dashboard';
$lang['als_clients'] = 'Customers';
$lang['als_affiliates'] = 'Affiliates';
$lang['als_affiliates_groups'] = 'Groups';
$lang['als_leads'] = 'Leads';

$lang['als_contracts'] = 'Contracts';

$lang['als_all_tickets'] = 'All Tickets';
$lang['als_sales'] = 'Sales';

$lang['als_staff'] = 'Staff';
$lang['als_tasks'] = 'Tasks';
$lang['als_kb'] = 'Knowledge Base';

$lang['als_surveys'] = 'Surveys';
$lang['als_media'] = 'Media';
$lang['als_reports'] = 'Reports';
$lang['als_reports_sales_submenu'] = 'Sales';
$lang['als_reports_leads_submenu'] = 'Leads';
$lang['als_kb_articles_submenu'] = 'KB Articles';
$lang['als_utilities'] = 'Utilities';
$lang['als_announcements_submenu'] = 'Announcements';
$lang['als_mail_lists_submenu'] = 'Mail Lists';
$lang['als_calendar_submenu'] = 'Calendar';
$lang['als_activity_log_submenu'] = 'Activity Log';

# Admin Customizer Sidebar
$lang['acs_tickets'] = 'Tickets';
$lang['acs_ticket_priority_submenu'] = 'Ticket Priority';
$lang['acs_ticket_statuses_submenu'] = 'Ticket Statuses';
$lang['acs_ticket_predefined_replies_submenu'] = 'Predifined Replies';
$lang['acs_ticket_services_submenu'] = 'Services';
$lang['acs_departments'] = 'Departments';
$lang['acs_leads'] = 'Leads';
$lang['acs_leads_sources_submenu'] = 'Sources';
$lang['acs_leads_statuses_submenu'] = 'Statuses';
$lang['acs_sales_taxes_submenu'] = 'Taxes';
$lang['acs_sales_currencies_submenu'] = 'Currencies';
$lang['acs_sales_payment_modes_submenu'] = 'Payment Modes';
$lang['acs_email_templates'] = 'Email Templates';
$lang['acs_roles'] = 'Roles';
$lang['acs_settings'] = 'Settings';

# Tickets
$lang['new_ticket'] = 'Open New Ticket';
$lang['tickets'] = 'Tickets';
$lang['ticket'] = 'Ticket';
$lang['ticket_lowercase'] = 'ticket';
$lang['support_tickets'] = 'Support Tickets';
$lang['support_ticket'] = 'Support Ticket';
$lang['ticket_settings_to'] = 'To';
$lang['ticket_settings_email'] = 'Email address';
$lang['ticket_settings_departments'] = 'Department';
$lang['ticket_settings_service'] = 'Service';
$lang['ticket_settings_priority'] = 'Priority';
$lang['ticket_settings_subject'] = 'Subject';
$lang['ticket_settings_assign_to'] = 'Assign ticket (default is current user)';
$lang['ticket_settings_assign_to_you'] = 'You';
$lang['ticket_settings_select_client'] = 'Select Client';
$lang['ticket_add_body'] = 'Ticket Body';
$lang['ticket_add_attachments'] = 'Attachments';
$lang['ticket_no_reply_yet'] = 'No Reply Yet';
$lang['new_ticket_added_succesfuly'] = 'Ticket #%s added successfuly';
$lang['replied_to_ticket_succesfuly'] = 'Replied to ticket #%s successfuly';
$lang['ticket_note_added_successfuly'] = 'Ticket note added successfuly';
$lang['ticket_note_deleted_successfuly'] = 'Ticket note deleted successfuly';
$lang['ticket_settings_updated_successfuly'] = 'Ticket settings updated successfuly';
$lang['ticket_settings_updated_successfuly_and_reassigned'] = 'Ticket settings updated successfuly - reasigned to department %s';
$lang['ticket_dt_subject'] = 'Subject';
$lang['ticket_dt_department'] = 'Department';
$lang['ticket_dt_service'] = 'Service';
$lang['ticket_dt_submitter'] = 'Client';
$lang['ticket_dt_status'] = 'Status';
$lang['ticket_dt_priority'] = 'Priority';
$lang['ticket_dt_last_reply'] = 'Last Reply';

$lang['ticket_single_add_reply'] = 'Add Reply';
$lang['ticket_single_add_note'] = 'Add note';
$lang['ticket_single_other_user_tickets'] = 'Other Tickets';
$lang['ticket_single_settings'] = 'Settings';
$lang['ticket_single_priority'] = 'Priority: %s';
$lang['ticket_single_last_reply'] = 'Last Reply: %s';
$lang['ticket_single_change_status_top'] = 'Change Status';
$lang['ticket_single_ticket_note_by'] = 'Ticket note by %s';
$lang['ticket_single_note_added'] = 'Note added: %s';
$lang['ticket_single_change_status'] = 'Change Status';
$lang['ticket_single_assign_to_me_on_update'] = 'Assign this ticket to me automatically';
$lang['ticket_single_insert_predefined_reply'] = 'Insert predefined reply';
$lang['ticket_single_insert_knowledge_base_link'] = 'Insert knowledge base link';
$lang['ticket_single_attachments'] = 'Attachments';
$lang['ticket_single_add_response'] = 'Add Response';
$lang['ticket_single_note_heading'] = 'Note';
$lang['ticket_single_add_note'] = 'Add note';
$lang['ticket_settings_none_assigned'] = 'None';
$lang['ticket_status_changed_successfuly'] = 'Ticket Status Changed';
$lang['ticket_status_changed_fail'] = 'Problem Changing Ticket Status';

$lang['ticket_staff_string'] = 'Staff';
$lang['ticket_client_string'] = 'Client';
$lang['ticket_posted'] = 'Posted: %s';
$lang['ticket_insert_predefined_reply_heading'] = 'Insert predefined reply';
$lang['ticket_kb_link_heading'] = 'Insert knowledge base link';
$lang['ticket_access_by_department_denied'] = 'You dont have access to this ticket. This ticket belongs to department that you are not assigned.';

# Staff
$lang['new_staff'] = 'New Staff Member';
$lang['staff_members'] = 'Staff Members';
$lang['staff_member'] = 'Staff Member';
$lang['staff_member_lowercase'] = 'staff member';
$lang['staff_profile_updated'] = 'Your Profile has Been Updated';
$lang['staff_old_password_incorect'] = 'Your old password is incorrect';
$lang['staff_password_changed'] = 'Your Password has been Changed';
$lang['staff_problem_changing_password'] = 'Problem changing your password';
$lang['staff_profile_string'] = 'Profile';

$lang['staff_cant_remove_main_admin'] = 'Cant remove main administrator';
$lang['staff_cant_remove_yourself_from_admin'] = 'You cant remove yourself the administrator role';

$lang['staff_dt_name'] = 'Full Name';
$lang['staff_dt_email'] = 'Email';
$lang['staff_dt_last_Login'] = 'Last Login';
$lang['staff_dt_active'] = 'Active';

$lang['staff_add_edit_firstname'] = 'First Name';
$lang['staff_add_edit_lastname'] = 'Last Name';
$lang['staff_add_edit_email'] = 'Email';
$lang['staff_add_edit_phonenumber'] = 'Phone';
$lang['staff_add_edit_facebook'] = 'Facebook';
$lang['staff_add_edit_linkedin'] = 'LinkedIn';
$lang['staff_add_edit_skype'] = 'Skype';
$lang['staff_add_edit_departments'] = 'Member departments';
$lang['staff_add_edit_role'] = 'Role';
$lang['staff_add_edit_permissions'] = 'Permissions';
$lang['staff_add_edit_administrator'] = 'Administrator';
$lang['staff_add_edit_password'] = 'Password';
$lang['staff_add_edit_password_note'] = 'Note: if you populate this fields, password will be changed on this member.';
$lang['staff_add_edit_password_last_changed'] = 'Password last changed';
$lang['staff_add_edit_notes'] = 'Notes';
$lang['staff_add_edit_note_description'] = 'Note description';
$lang['staff_no_user_notes_found'] = 'No user notes found';

$lang['staff_notes_table_description_heading'] = 'Note';
$lang['staff_notes_table_addedfrom_heading'] = 'Added From';
$lang['staff_notes_table_dateadded_heading'] = 'Date Added';

$lang['staff_admin_profile'] = 'This is admin profile';
$lang['staff_profile_notifications'] = 'Notifications';
$lang['staff_profile_departments'] = 'Departments';

$lang['staff_edit_profile_image'] = 'Profile Image';
$lang['staff_edit_profile_your_departments'] = 'Your Departments';
$lang['staff_edit_profile_change_your_password'] = 'Change your password';
$lang['staff_edit_profile_change_old_password'] = 'Old password';
$lang['staff_edit_profile_change_new_password'] = 'New password';
$lang['staff_edit_profile_change_repet_new_password'] = 'Repeat new password';

# Surveys
$lang['new_survey'] = 'New Survey';
$lang['surveys'] = 'Surveys';
$lang['survey'] = 'Survey';
$lang['survey_lowercase'] = 'survey';
$lang['survey_no_mail_lists_selected'] = 'No mail lists selected';
$lang['survey_send_success_note'] = 'All Survey Emails(%s) will be send via CRON job with interval 5 minutes';
$lang['survey_result'] = 'Result for Survey: %s';
$lang['question_string'] = 'Question';
$lang['question_field_string'] = 'Field';

$lang['survey_list_view_tooltip'] = 'View Survey';
$lang['survey_list_view_results_tooltip'] = 'View Results';

$lang['survey_add_edit_subject'] = 'Survey subject';
$lang['survey_add_edit_email_description'] = 'Survey description (Email Description)';
$lang['survey_include_survey_link'] = 'Include survey link in description';
$lang['survey_available_mail_lists_custom_fields'] = 'Available custom fields from email lists';
$lang['survey_mail_lists_custom_fields_tooltip'] = 'Custom fields can be used for email editor. TIP: Click on the email editor and then select from dropdown menu to be appended automaticaly.';
$lang['survey_add_edit_short_description_view'] = 'Survey short description (View Description)';
$lang['survey_add_edit_from'] = 'From (dislayed in email)';
$lang['survey_add_edit_redirect_url'] = 'Survey redirect URL';
$lang['survey_add_edit_red_url_note'] = 'When user finish survey where to be redirected (leave blank for this site url)';
$lang['survey_add_edit_disabled'] = 'Disabled';
$lang['survey_add_edit_only_for_logged_in'] = 'Only for logged in participants (staff,customers)';
$lang['send_survey_string'] = 'Send Survey';
$lang['survey_send_mail_list_clients'] = 'Customers';
$lang['survey_send_mail_list_staff'] = 'Staff';
$lang['survey_send_mail_lists_string'] = 'Mail Lists';
$lang['survey_send_mail_lists_note_logged_in'] = 'Note: If you are sending survey to mail lists Only for logged in participants need to be unchecked';
$lang['survey_send_string'] = 'Send';

$lang['survey_send_to_total'] = 'Send to total %s emails';
$lang['survey_send_till_now'] = 'Till now';
$lang['survey_send_finished'] = 'Survey send finished: %s';
$lang['survey_added_to_queue'] = 'This survey is added to cron queue on %s';

$lang['survey_questions_string'] = 'Questions';
$lang['survey_insert_field'] = 'Insert Field';
$lang['survey_field_checkbox'] = 'Checkbox';
$lang['survey_field_radio'] = 'Radio';
$lang['survey_field_input'] = 'Input Field';
$lang['survey_field_textarea'] = 'Textarea';
$lang['survey_question_required'] = 'Required';
$lang['survey_question_only_for_preview'] = 'Only for preview';
$lang['survey_create_first'] = 'You need to create the survey first then you will be able to insert the questions.';


$lang['survey_dt_name'] = 'Name';
$lang['survey_dt_total_questions'] = 'Total Questions';
$lang['survey_dt_total_participants'] = 'Total Participants';
$lang['survey_dt_date_created'] = 'Date Created';
$lang['survey_dt_active'] = 'Active';

$lang['survey_text_questions_results'] = 'Text questions result';
$lang['survey_view_all_answers'] = 'View all answers';

# Staff Tasks
$lang['new_task'] = 'New Task';
$lang['tasks'] = 'Tasks';
$lang['task'] = 'Task';
$lang['task_lowercase'] = 'task';
$lang['comment_string'] = 'Comment';

$lang['task_marked_as_complete'] = 'Task marked as complete';
$lang['task_follower_removed'] = 'Task follower removed successfuly';
$lang['task_assignee_removed'] = 'Task assignee removed successfuly';

$lang['task_no_assignees'] = 'No assignees for this task';
$lang['task_no_followers'] = 'No followers for this task';

$lang['task_sort_finished'] = 'Finished';
$lang['task_sort_priority'] = 'Priority';
$lang['task_sort_startdate'] = 'Start Date';
$lang['task_sort_dateadded'] = 'Date Created';
$lang['task_sort_duedate'] = 'Due Date';

$lang['task_list_all'] = 'All';
$lang['task_list_finished'] = 'Finished';
$lang['task_list_unfinished'] = 'Unfinished';
$lang['task_list_not_assigned'] = 'Not Assigned';
$lang['task_list_duedate_passed'] = 'Due Date Passed';
$lang['tasks_dt_name'] = 'Name';

$lang['task_single_priority'] = 'Priority';
$lang['task_single_start_date'] = 'Start Date';
$lang['task_single_due_date'] = 'Due Date';
$lang['task_single_finished'] = 'Finished';
$lang['task_single_mark_as_complete'] = 'Mark as complete';
$lang['task_single_edit'] = 'Edit';
$lang['task_single_delete'] = 'Delete';
$lang['task_single_assignees'] = 'Assignees';
$lang['task_single_assignees_select_title'] = 'Assign task to';
$lang['task_single_followers'] = 'Followers';
$lang['task_single_followers_select_title'] = 'Add Followers';
$lang['task_single_insert_media_link'] = 'Insert Media Link';
$lang['task_single_add_new_comment'] = 'Add Comment';

$lang['task_add_edit_subject'] = 'Subject';
$lang['task_add_edit_priority'] = 'Priority';
$lang['task_priority_low'] = 'Low';
$lang['task_priority_medium'] = 'Medium';
$lang['task_priority_high'] = 'High';
$lang['task_priority_urgent'] = 'Urgent';
$lang['task_add_edit_start_date'] = 'Start Date';
$lang['task_add_edit_due_date'] = 'Due Date';
$lang['task_add_edit_description'] = 'Task Description';

# Taxes
$lang['new_tax'] = 'New Tax';
$lang['taxes'] = 'Taxes';
$lang['tax'] = 'Tax';
$lang['tax_lowercase'] = 'tax';
$lang['tax_dt_name'] = 'Tax Name';
$lang['tax_dt_rate'] = 'Rate (percent)';

$lang['tax_add_edit_name'] = 'Tax Name';
$lang['tax_add_edit_rate'] = 'Tax Rate (percent)';
$lang['tax_edit_title'] = 'Edit Tax';
$lang['tax_add_title'] = 'Add New Tax';

# Ticket Statuses
$lang['new_ticket_status'] = 'New Ticket Status';
$lang['ticket_statuses'] = 'Ticket Statuses';
$lang['ticket_status'] = 'Ticket Status';
$lang['ticket_status_lowercase'] = 'ticket status';

$lang['ticket_statuses_dt_name'] = 'Ticket Status Name';
$lang['no_ticket_statuses_found'] = 'No ticket statuses found';
$lang['ticket_statuses_table_total'] = 'Total %s';
$lang['ticket_status_add_edit_name'] = 'Ticket Status Name';
$lang['ticket_status_add_edit_color'] = 'Pick Color';
$lang['ticket_status_add_edit_order'] = 'Status Order';

# Todos
$lang['new_todo'] = 'New Todo';
$lang['my_todos'] = 'My Todo Items';
$lang['todo'] = 'Todo Item';
$lang['todo_lowercase'] = 'todo';
$lang['todo_status_changed'] = 'Todo Status Changed';
$lang['todo_add_title'] = 'Add New Todo';
$lang['add_new_todo_description'] = 'Description';
$lang['no_finished_todos_found'] = 'No finished todos found';
$lang['no_unfinished_todos_found'] = 'No todos found';
$lang['unfinished_todos_title'] = 'Unfinished todo\'s';
$lang['finished_todos_title'] = 'Latest finished todo\'s';

# Authentication
$lang['password_changed_email_subject'] = 'Your password has been changed';
$lang['password_reset_email_subject'] = 'Reset your password on %s';
# Utilities
$lang['utility_activity_log'] = 'Activity Log';
$lang['utility_activity_log_filter_by_date'] = 'Filter by date';
$lang['utility_activity_log_dt_description'] = 'Description';
$lang['utility_activity_log_dt_date'] = 'Date';
$lang['utility_activity_log_dt_staff'] = 'Staff';
$lang['utility_calendar_new_event_title'] = 'Add new event';
$lang['utility_calendar_new_event_start_date'] = 'Start Date';
$lang['utility_calendar_new_event_end_date'] = 'End Date';
$lang['utility_calendar_new_event_make_public'] = 'Make Public';
$lang['utility_calendar_event_added_successfuly'] = 'New event added succesfuly';
$lang['utility_calendar_event_deleted_successfuly'] = 'Event deleted';
$lang['utility_calendar_new_event_placeholder'] = 'Event title';


# Navigation
$lang['nav_notifications'] = 'Notifications';
$lang['nav_my_profile'] = 'My Profile';
$lang['nav_edit_profile'] = 'Edit Profile';
$lang['nav_logout'] = 'Logout';
$lang['nav_no_notifications'] = 'No notifications found';
$lang['nav_view_all_notifications'] = 'View all notifications';
$lang['nav_customizer_tooltip'] = 'Customize Settings';
$lang['nav_notifications_tooltip'] = 'View Notifications';
$lang['nav_sidebar_toggle_tooltip'] = 'Toggle Sidebar';



## Clients
#

$lang['clients_required_field'] = 'This field is required';

# Footer
$lang['clients_copyright'] = 'Copyright %s';

# Announcements
$lang['clients_announcement_from'] = 'From: ';
$lang['clients_announcement_added'] = 'Added: ';

# Contracts
$lang['clients_contracts'] = 'Contracts';
$lang['clients_contracts_dt_subject'] = 'Subject';
$lang['clients_contracts_dt_start_date'] = 'Start Date';
$lang['clients_contracts_dt_end_date'] = 'End Date';

# Home
$lang['clients_quick_invoice_info'] = 'Quick Invoices Info';
$lang['clients_home_currency_select_tooltip'] = 'You need to select currency becuase you have invoices with different currency';
$lang['clients_report_sales_months_all_time'] = 'All Time';
$lang['clients_report_sales_months_six_months'] = 'Last 6 months';
$lang['clients_report_sales_months_twelve_months'] = 'Last 12 months';
$lang['clients_report_sales_months_custom'] = 'Custom';
$lang['clients_report_select_from_date'] = 'From Date';
$lang['clients_report_select_to_date'] = 'To Date';

# Invoices
$lang['clients_invoice_html_btn_download'] = 'Download';

$lang['clients_my_invoices'] = 'My Invoices';
$lang['clients_invoice_dt_number'] = 'Invoice #';
$lang['clients_invoice_dt_date'] = 'Date';
$lang['clients_invoice_dt_duedate'] = 'Due Date';
$lang['clients_invoice_dt_amount'] = 'Amount';
$lang['clients_invoice_dt_status'] = 'Status';

# Profile
$lang['clients_profile_heading'] = 'Profile';

# Used for edit profile and register START
$lang['clients_firstname'] = 'First Name';
$lang['clients_lastname'] = 'Last Name';
$lang['clients_email'] = 'Email Address';
$lang['clients_company'] = 'Company';
$lang['clients_vat'] = 'VAT Number';
$lang['clients_phone'] = 'Phone';
$lang['clients_country'] = 'Country';
$lang['clients_city'] = 'City';
$lang['clients_address'] = 'Address';
$lang['clients_zip'] = 'Zip';
$lang['clients_state'] = 'State';

# Used for edit profile and register END

$lang['clients_register_password'] = 'Password';
$lang['clients_register_password_repeat'] = 'Repeat Password';
$lang['clients_edit_profile_update_btn'] = 'Update';

$lang['clients_edit_profile_change_password_heading'] = 'Change Password';
$lang['clients_edit_profile_old_password'] = 'Old Password';
$lang['clients_edit_profile_new_password'] = 'New Password';
$lang['clients_edit_profile_new_password_repeat'] = 'Repeat Password';
$lang['clients_edit_profile_change_password_btn'] = 'Change Password';
$lang['clients_profile_last_changed_password'] = 'Password last changed %s';

# Knowledge base
$lang['clients_knowledge_base'] = 'Knowledge Base';
$lang['clients_knowledge_base_articles_not_found'] = 'No knowledge base articles found';
$lang['clients_knowledge_base_find_useful'] = 'Did you find this article useful?';
$lang['clients_knowledge_base_find_useful_yes'] = 'Yes';
$lang['clients_knowledge_base_find_useful_no'] = 'No';
$lang['clients_article_only_1_vote_today'] = 'You can vote once in 24 hours';
$lang['clients_article_voted_thanks_for_feedback'] = 'Thanks for your feedback';

# Tickets
$lang['clients_ticket_open_subject'] = 'Open Ticket';
$lang['clients_ticket_open_departments'] = 'Department';
$lang['clients_tickets_heading'] = 'Support Tickets';
$lang['clients_ticket_open_service'] = 'Service';
$lang['clients_ticket_open_priority'] = 'Priority';
$lang['clients_latest_tickets'] = 'Latest Tickets';
$lang['clients_ticket_open_body'] = 'Ticket Body';
$lang['clients_ticket_attachments'] = 'Attachments';
$lang['clients_ticket_posted'] = 'Posted: %s';
$lang['clients_single_ticket_string'] = 'Ticket';
$lang['clients_single_ticket_replied'] = 'Replied: %s';
$lang['clients_single_ticket_informations_heading'] = 'Ticket Informations';

$lang['clients_tickets_dt_number'] = 'Ticket #';
$lang['clients_tickets_dt_subject'] = 'Subject';
$lang['clients_tickets_dt_department'] = 'Department';
$lang['clients_tickets_dt_service'] = 'Service';
$lang['clients_tickets_dt_status'] = 'Status';
$lang['clients_tickets_dt_last_reply'] = 'Last Reply';


$lang['clients_ticket_single_department'] = 'Department: %s';
$lang['clients_ticket_single_submited'] = 'Submited: %s';
$lang['clients_ticket_single_status'] = 'Status:';
$lang['clients_ticket_single_priority'] = 'Priority: %s';
$lang['clients_ticket_single_add_reply_btn'] = 'Add Reply';
$lang['clients_ticket_single_add_reply_heading'] = 'Add reply to this ticket';

# Login
$lang['clients_login_heading_no_register'] = 'Please login';
$lang['clients_login_heading_register'] = 'Please login or register';
$lang['clients_login_email'] = 'Email Address';
$lang['clients_login_password'] = 'Password';
$lang['clients_login_remember'] = 'Remember me';
$lang['clients_login_login_string'] = 'Login';

# Register
$lang['clients_register_string'] = 'Register';
$lang['clients_register_heading'] = 'Register';

# Navigation
$lang['clients_nav_login'] = 'Login';
$lang['clients_nav_register'] = 'Register';
$lang['clients_nav_invoices'] = 'Invoices';
$lang['clients_nav_contracts'] = 'Contracts';
$lang['clients_nav_kb'] = 'Knowledge Base';
$lang['clients_nav_profile'] = 'Profile';
$lang['clients_nav_logout'] = 'Logout';

# Datatables
$lang['clients_dt_paginate_first'] = 'First';
$lang['clients_dt_paginate_last'] = 'Last';
$lang['clients_dt_paginate_next'] = 'Next';
$lang['clients_dt_paginate_previous'] = 'Previous';
$lang['clients_dt_empty_table'] = 'No {0} found';
$lang['clients_dt_search'] = 'Search:';
$lang['clients_dt_zero_records'] = 'No matching records found';
$lang['clients_dt_loading_records'] = 'Loading...';
$lang['clients_dt_length_menu'] = 'Show _MENU_ ';
$lang['clients_dt_info_filtered'] = '(filtered from _MAX_ total {0})';
$lang['clients_dt_info_empty'] = 'Showing 0 to 0 of 0 {0}';
$lang['clients_dt_info'] = 'Showing _START_ to _END_ of _TOTAL_ {0}';
$lang['clients_dt_empty_table'] = 'No {0} found';
$lang['clients_dt_sort_ascending'] = 'activate to sort column ascending';
$lang['clients_dt_sort_descending'] = 'activate to sort column descending';


# Version 1.0.1
# Admin
#
# Payments
$lang['payment_receipt'] = 'Payment Receipt';
$lang['payment_for_string'] = 'Payment For';
$lang['payment_date'] = 'Payment Date:';
$lang['payment_view_mode'] = 'Payment Mode:';
$lang['payment_total_amount'] = 'Total Amount';
$lang['payment_table_invoice_number'] = 'Invoice Number';
$lang['payment_table_invoice_date'] = 'Invoice Date';
$lang['payment_table_invoice_amount_total'] = 'Invoice Amount';
$lang['payment_table_payment_amount_total'] = 'Payment Amount';
$lang['payments_table_transaction_id'] = 'Transaction ID: %s';
$lang['payment_getaway_token_not_found'] = 'Token Not Found';
$lang['online_payment_recorded_success'] = 'Payment recorded successfuly';
$lang['online_payment_recorded_success_fail_database'] = 'Payment is successful but failed to insert payment to database, contact administrator';

# Leads
$lang['lead_convert_to_client'] = 'Convert to client';
$lang['lead_convert_to_email'] = 'Email';
$lang['lead_convert_to_client_firstname'] = 'First Name';
$lang['lead_convert_to_client_lastname'] = 'Last Name';
$lang['lead_email_already_exists'] = 'Lead email already exists in customers data';
$lang['lead_to_client_base_converted_success'] = 'Lead converted to client successfuly';
$lang['lead_already_converted'] = 'Converted to client';
$lang['lead_have_client_profile'] = 'This lead have client profile.';
$lang['lead_converted_edit_client_profile'] = 'Edit Profile';
$lang['lead_is_client_cant_change_status_canban'] = 'This lead is converted to client. You cant change his status.';

# Invoices
$lang['view_invoice_as_customer_tooltip'] = 'View Invoice as Client';
$lang['invoice_add_edit_recurring'] = 'Recurring Invoice?';
$lang['invoice_add_edit_recurring_no'] = 'No';
$lang['invoice_add_edit_recurring_month'] = 'Every %s month';
$lang['invoice_add_edit_recurring_months'] = 'Every %s months';
$lang['invoices_list_all'] = 'All';
$lang['invoices_list_tooltip'] = 'View Invoices';
$lang['invoices_list_not_sent'] = 'Invoice Not Sent';
$lang['invoices_list_not_have_payment'] = 'Invoices with no payment record';
$lang['invoices_list_recuring'] = 'Recurring Invoices';
$lang['invoices_list_made_payment_by'] = 'Made Payment by %s';
$lang['invoices_create_invoice_from_recurring_only_on_paid_invoices'] = 'Create new invoice from main recurring invoice only if is with status Paid';
$lang['invoices_create_invoice_from_recurring_only_on_paid_invoices_tooltip'] = 'Create new invoice from the main recurring invoice only if the main invoice is with status paid? If this field is set to No and the recurring invoice is not with status paid the new invoice wont be created';
$lang['send_renewed_invoice_from_recurring_to_email'] = 'Automatically send the renewed invoice to the client';
$lang['view_invoice_pdf_link_pay'] = 'Pay Invoice';

# Payment modes
$lang['payment_mode_add_edit_description'] = 'Bank Accounts / Description';
$lang['payment_mode_add_edit_description_tooltip'] = 'You can set here bank accounts informations. Will be shown on HTML Invoice';
$lang['payment_modes_dt_description'] = 'Bank Accounts / Description';
$lang['payment_modes_add_edit_announcement'] = 'Note: Payment modes listed below are offline modes. Online payment modes can be configured in Settings-> Payment Getaways';
$lang['payment_mode_add_edit_active'] = 'Active';
$lang['payment_modes_dt_active'] = 'Active';

# Contracts
$lang['contract_not_found'] = 'Contract not found. Maybe is deleted, check activity log';

# Settings
$lang['setting_bar_heading'] = 'Setup';
$lang['settings_group_online_payment_modes'] = 'Payment Getaways';
$lang['settings_paymentmethod_mode_label'] = 'Label';
$lang['settings_paymentmethod_active'] = 'Active';
$lang['settings_paymentmethod_currencies'] = 'Coma Separated Currencies';
$lang['settings_paymentmethod_testing_mode'] = 'Enable Testing Mode';

$lang['settings_paymentmethod_paypal_username'] = 'Paypal API Username';
$lang['settings_paymentmethod_paypal_password'] = 'Paypal API Password';
$lang['settings_paymentmethod_paypal_signature'] = 'API Signature';

$lang['settings_paymentmethod_stripe_api_secret_key'] = 'Stripe API Secret Key';
$lang['settings_paymentmethod_stripe_api_publishable_key'] = 'Stripe Publishable Key';
$lang['settings_limit_top_search_bar_results'] = 'Limit Top Search Bar Results to';

# Quick Actions
$lang['qa_create_invoice'] = 'Create Invoice';
$lang['qa_create_task'] = 'Create Task';
$lang['qa_create_client'] = 'Create Customer';
$lang['qa_create_contract'] = 'Create Contract';
$lang['qa_create_kba'] = 'Create Knowledge Base Article';
$lang['qa_create_survey'] = 'Create Survey';
$lang['qa_create_ticket'] = 'Open Ticket';
$lang['qa_create_staff'] = 'Create Staff Member';

## Clients
$lang['client_phonenumber'] = 'Phone';

# Main Clients
$lang['clients_register'] = 'Register';
$lang['clients_profile_updated'] = 'Your profile has been updated';
$lang['clients_successfully_registered'] = 'Thank your for registering';
$lang['clients_account_created_but_not_logged_in'] = 'Your account has been created but you are not logged in our system automatically. Please try to login';
# Tickets
$lang['clients_tickets_heading'] = 'Support Tickets';

# Payments
// Uses on stripe page
$lang['payment_for_invoice'] = 'Payment for Invoice';
$lang['payment_total'] = 'Total: %s';

# Invoice
$lang['invoice_html_online_payment'] = 'Online Payment';
$lang['invoice_html_online_payment_button_text'] = 'Pay Now';
$lang['invoice_html_payment_modes_not_selected'] = 'Please Select Payment Mode';
$lang['invoice_html_amount_blank'] = 'Total amount cant be blank or zero';
$lang['invoice_html_offline_payment'] = 'Offline Payment';
$lang['invoice_html_amount'] = 'Amount';


# Version 1.0.2
# Admin
#
# DataTables
$lang['dt_column_visibility_tooltip'] = 'You can use column visibility also to adjust the export columns. By default all columns will be exported.';
$lang['dt_button_column_visibility'] = 'Visibility';
$lang['dt_button_reload'] = 'Reload';
$lang['dt_button_excel'] = 'Excel';
$lang['dt_button_csv'] = 'CSV';
$lang['dt_button_pdf'] = 'PDF';
$lang['dt_button_print'] = 'Print';
$lang['is_not_active_export'] = 'No';
$lang['is_active_export'] = 'Yes';

# Invoice
$lang['invoice_add_edit_advanced_options'] = 'Advanced Options';
$lang['invoice_add_edit_allowed_payment_modes'] = 'Allowed payment modes for this invoice';
$lang['invoice_add_edit_recuring_invoices_from_invoice'] = 'Recurring invoices from this invoice';
$lang['invoice_add_edit_no_payment_modes_found'] = 'No payment modes found.';
$lang['invoice_html_total_pay'] = 'Total: %s';

# Email templates
$lang['email_templates_table_heading_name'] = 'Template Name';
# General
$lang['discount_type'] = 'Discount Type';
$lang['discount_type_after_tax'] = 'After Tax';
$lang['discount_type_before_tax'] = 'Before Tax';
$lang['terms_and_conditions'] = 'Terms & Conditions';
$lang['reference_no'] = 'Reference #';
$lang['no_discount'] = 'No discount';
$lang['view_stats_tooltip'] = 'View Quick Stats';
# Clients
$lang['zip_from_date'] = 'From Date:';
$lang['zip_to_date'] = 'To Date:';
$lang['client_zip_payments'] = 'ZIP Payments';
$lang['client_zip_invoices'] = 'ZIP Invoices';
$lang['client_zip_estimates'] = 'ZIP Estimates';
$lang['client_zip_status'] = 'Status';
$lang['client_zip_status_all'] = 'All';
$lang['client_zip_payment_modes'] = 'Payment made by';
$lang['client_zip_no_data_found'] = 'No %s found';

# Payments
$lang['payment_mode'] = 'Payment Mode';
$lang['payment_view_heading'] = 'Payment';

# Settings
$lang['settings_allow_payment_amount_to_be_modified'] = 'Allow customer to modify the amount to pay (for online payments)';
$lang['settings_survey_send_emails_per_cron_run'] = 'How much emails to sent when CRON run (Surveys)';
$lang['settings_survey_send_emails_per_cron_run_tooltip'] = 'This option is used when sending Surveys. The Survey cron runs every 5 minutes. So you can set how much email to be sent every 5 minutes';
$lang['settings_delete_only_on_last_invoice'] = 'Delete invoice allowed only on last invoice';
$lang['settings_sales_estimate_prefix'] = 'Estimate Number Prefix';
$lang['settings_sales_next_estimate_number'] = 'Next estimate Number';
$lang['settings_sales_next_estimate_number_tooltip'] = 'Set this field to 1 if you want to start from begining';
$lang['settings_sales_decrement_estimate_number_on_delete'] = 'Decrement estimate number on delete';
$lang['settings_sales_decrement_estimate_number_on_delete_tooltip'] = 'Do you want to decrement the estimate number when the last estimate is deleted? Ex. If is set this option to YES and before estimate delete the next estimate number is 15 the next estimate number will decrement to 14 for the next estimate if is set to NO the number will remain to 15';
$lang['settings_sales_estimate_number_format'] = 'Estimate Number Format';
$lang['settings_sales_estimate_number_format_year_based'] = 'Year Based';
$lang['settings_sales_estimate_number_format_number_based'] = 'Number Based (000001)';
$lang['settings_sales_estimate_year'] = 'Estimate Year (YYYY/000001)';
$lang['settings_sales_estimate_year_tooltip'] = 'Current estimate year. Reset this when new year arrives.';
$lang['settings_delete_only_on_last_estimate'] = 'Delete estimate allowed only on last invoice';
$lang['settings_cron_invoice_heading'] = 'Invoice';
$lang['settings_send_test_email_heading'] = 'Send Test Email';
$lang['settings_send_test_email_subheading'] = 'Send test email to make sure that your SMTP settings is set correctly.';
$lang['settings_send_test_email_string'] = 'Email Address';
$lang['settings_smtp_settings_heading'] = 'SMTP Settings';
$lang['settings_smtp_settings_subheading'] = 'Setup main email';
$lang['settings_getaways_heading_notice'] = 'For security reasons online payment getaways are visible only to user with ID 1.Which means to user that installed the CRM.';
$lang['settings_sales_heading_general'] = 'General';
$lang['settings_sales_heading_invoice'] = 'Invoice';
$lang['settings_sales_heading_estimates'] = 'Estimates';
$lang['settings_sales_heading_company'] = 'Company';
$lang['settings_sales_cron_invoice_heading'] = 'Invoice';

# Tasks
$lang['tasks_dt_datestart'] = 'Date Start';
$lang['tasks_dt_priority'] = 'Priority';

# Invoice General
$lang['invoice_discount'] = 'Discount';

# Tickets
$lang['ticket_settings_client'] = 'Client';

# Settings
$lang['settings_rtl_support_admin'] = 'RTL Admin Area (Right to Left) BETA';
$lang['settings_rtl_support_client'] = 'RTL Client Area (Right to Left) BETA';
$lang['acs_language_editor'] = 'Language Editor';
$lang['settings_estimate_auto_convert_to_invoice_on_client_accept'] = 'Auto convert the estimate to invoice after client accept';
$lang['settings_exclude_estimate_from_client_area_with_draft_status'] = 'Exclude estimates with draft status from client area';

# Months
$lang['January'] = 'January';
$lang['February'] = 'February';
$lang['March'] = 'March';
$lang['April'] = 'April';
$lang['May'] = 'May';
$lang['June'] = 'June';
$lang['July'] = 'July';
$lang['August'] = 'August';
$lang['September'] = 'September';
$lang['October'] = 'October';
$lang['November'] = 'November';
$lang['December'] = 'December';

# Time ago function translate
$lang['time_ago_just_now'] = 'Just now';
$lang['time_ago_minute'] = 'one minute ago';
$lang['time_ago_minutes'] = '%s minutes ago';
$lang['time_ago_hour'] = 'an hour ago';
$lang['time_ago_hours'] = '%s hrs ago';
$lang['time_ago_yesterday'] = 'yesterday';
$lang['time_ago_days'] = '%s days ago';
$lang['time_ago_week'] = 'a week ago';
$lang['time_ago_weeks'] = '%s weeks ago';
$lang['time_ago_month'] = 'a month ago';
$lang['time_ago_months'] = '%s months ago';
$lang['time_ago_year'] = 'one year ago';
$lang['time_ago_years'] = '%s years ago';


# Estimates
$lang['estimates'] = 'Estimates';
$lang['estimate'] = 'Estimate';
$lang['estimate_lowercase'] = 'estimate';
$lang['create_new_estimate'] = 'Create New Estimate';
$lang['view_estimate'] = 'View estimate';
$lang['estimate_number_changed'] = 'Estimate created successfuly but the number is changed becuase someone added new estimate before you.';
$lang['estimate_sent_to_client_success'] = 'The estimate is sent successfuly to the client';
$lang['estimate_sent_to_client_fail'] = 'Problem while sending the estimate';
$lang['estimate_reminder_send_problem'] = 'Problem sending estimate overdue reminder';
$lang['estimate_details'] = 'Estimate Details';
$lang['estimate_view'] = 'View estimate';
$lang['estimate_select_customer'] = 'Customer';
$lang['estimate_add_edit_number'] = 'Estimate Number';
$lang['estimate_add_edit_date'] = 'Estimate Date';
$lang['estimate_add_edit_expirydate'] = 'Expiry Date';
$lang['estimate_add_edit_currency'] = 'Currency';
$lang['estimate_add_edit_client_note'] = 'Client Note';
$lang['estimate_add_edit_admin_note'] = 'Admin Note';
$lang['estimate_add_edit_new_item'] = 'New Item';
$lang['estimate_add_edit_search_item'] = 'Search Items';
$lang['estimates_toggle_table_tooltip'] = 'View Full Table';
$lang['estimate_add_edit_advanced_options'] = 'Advanced Options';
$lang['estimate_vat'] = 'VAT Number';
$lang['estimate_to'] = 'To';
$lang['estimates_list_all'] = 'All';
$lang['estimates_list_tooltip'] = 'View Estimates';

$lang['estimate_invoiced_date'] = 'Estimate Invoiced on %s';
$lang['edit_estimate_tooltip'] = 'Edit Estimate';
$lang['delete_estimate_tooltip'] = 'Delete Estimate';
$lang['estimate_sent_to_email_tooltip'] = 'Send to Email';
$lang['estimate_already_send_to_client_tooltip'] = 'This estimate is already sent to the client %s';
$lang['send_overdue_notice_tooltip'] = 'Send Overdue Notice';
$lang['estimate_view_activity_tooltip'] = 'Activity Log';

$lang['estimate_send_to_client_modal_heading'] = 'Send this estimate to client';
$lang['estimate_send_to_client_attach_pdf'] = 'Attach estimate PDF';
$lang['estimate_send_to_client_preview_template'] = 'Preview Email Template';

$lang['estimate_dt_table_heading_number'] = 'Estimate #';
$lang['estimate_dt_table_heading_date'] = 'Date';
$lang['estimate_dt_table_heading_client'] = 'Client';
$lang['estimate_dt_table_heading_expirydate'] = 'Expiry Date';
$lang['estimate_dt_table_heading_amount'] = 'Amount';
$lang['estimate_dt_table_heading_status'] = 'Status';

$lang['estimate_email_link_text'] = 'View Estimate';
$lang['estimate_convert_to_invoice'] = 'Convert to Invoice';
# Home
$lang['home_unfinished_tasks'] = 'Unfinished Tasks';

# Clients
$lang['client_estimates_tab'] = 'Estimates';
$lang['client_payments_tab'] = 'Payments';


# Estimate General
$lang['estimate_pdf_heading'] = 'ESTIMATE';
$lang['estimate_table_item_heading'] = 'Item';
$lang['estimate_table_quantity_heading'] = 'Qty';
$lang['estimate_table_rate_heading'] = 'Rate';
$lang['estimate_table_tax_heading'] = 'Tax';
$lang['estimate_table_amount_heading'] = 'Amount';
$lang['estimate_subtotal'] = 'Sub Total';
$lang['estimate_adjustment'] = 'Adjustment';
$lang['estimate_discount'] = 'Discount';
$lang['estimate_total'] = 'Total';
$lang['estimate_to'] = 'To';
$lang['estimate_data_date'] = 'Estimate Date';
$lang['estimate_data_expiry_date'] = 'Expiry Date';
$lang['estimate_note'] = 'Note:';
$lang['estimate_status_draft'] = 'Draft';
$lang['estimate_status_sent'] = 'Sent';
$lang['estimate_status_declined'] = 'Declined';
$lang['estimate_status_accepted'] = 'Accepted';
$lang['estimate_status_expired'] = 'Expired';
$lang['estimate_note'] = 'Note:';

# Quick create
$lang['qa_create_estimate'] = 'Create Estimate';
$lang['qa_create_lead'] = 'Create Lead';


## Clients
$lang['clients_estimate_dt_number'] = 'Estimate #';
$lang['clients_estimate_dt_date'] = 'Date';
$lang['clients_estimate_dt_duedate'] = 'Expiry Date';
$lang['clients_estimate_dt_amount'] = 'Amount';
$lang['clients_estimate_dt_status'] = 'Status';
$lang['clients_nav_estimates'] = 'Estimates';
$lang['clients_decline_estimate'] = 'Decline';
$lang['clients_accept_estimate'] = 'Accept';
$lang['clients_my_estimates'] = 'Estimates';
$lang['clients_estimate_invoiced_successfuly'] = 'Estimate accepted. Here is the invoice from this estimate';
$lang['clients_estimate_accepted_not_invoiced'] = 'Thank you for accepting this estimate';
$lang['clients_estimate_declined'] = 'Estimate declined. You can accept the estimate any time before expiry date';
$lang['clients_estimate_failed_action'] = 'Failed to take action on this estimate';
$lang['client_add_edit_profile'] = 'Profile';

# Version 1.0.3
# Admin
# Home
$lang['home_invoice_not_sent'] = 'Invoice Not Sent';
$lang['home_expired_estimates'] = 'Expired Estimates';
$lang['home_invoice_overdue'] = 'Invoice Overdue';
$lang['home_payments_received_today'] = 'Payments Received Today';

# Reports

# Custom Fields
$lang['custom_field'] = 'Custom field';
$lang['custom_field_lowercase'] = 'custom field';
$lang['custom_fields'] = 'Custom Fields';
$lang['custom_fields_lowercase'] = 'custom fields';
$lang['new_custom_field'] = 'New Custom Field';
$lang['custom_field_name'] = 'Field Name';
$lang['custom_field_add_edit_type'] = 'Type';
$lang['custom_field_add_edit_belongs_top'] = 'Field Belongs to';
$lang['custom_field_add_edit_options'] = 'Options';
$lang['custom_field_add_edit_options_tooltip'] = 'Only use for Select types. Populate the field by separating the options by coma. Ex. apple,orange,banana';
$lang['custom_field_add_edit_order'] = 'Order';

$lang['custom_field_dt_field_to'] = 'Belongs to';
$lang['custom_field_dt_field_name'] = 'Name';
$lang['custom_field_dt_field_type'] = 'Type';
$lang['custom_field_add_edit_active'] = 'Active';
$lang['custom_field_add_edit_disabled'] = 'Disabled';

# Ticket replies
$lang['ticket_reply'] = 'Ticket Reply';
$lang['ticket_reply_lowercase'] = 'ticket reply';

# Admin Customizer Sidebar
$lang['asc_custom_fields'] = 'Custom Fields';

# Contracts
$lang['contract_types'] = 'Contracts Types';
$lang['contract_type'] = 'Contract type';
$lang['new_contract_type'] = 'New Contract Type';
$lang['contract_type_lowercase'] = 'contract';
$lang['contract_type_name'] = 'Name';

$lang['contract_types_list_name'] = 'Contract Type';

# Customizer Menu
$lang['acs_contracts'] = 'Contracts';
$lang['acs_contract_types'] = 'Contract Types';

# Version 1.0.4
# Invoice Items
$lang['invoice_item_long_description'] = 'Long Description';
# Customers
$lang['client_delete_invoices_warning'] = 'This client have invoices or estimates on the account. You cant delete this client. Change all invoices to another client in a future then delete.';
$lang['clients_list_phone'] = 'Phone';
$lang['client_expenses_tab'] = 'Expenses';
$lang['customers_summary'] = 'Customers Summary';
$lang['customers_summary_active'] = 'Active';
$lang['customers_summary_inactive'] = 'Inactive';
$lang['customers_summary_companies'] = 'Companies';
$lang['customers_summary_individual'] = 'Individual';
$lang['customers_summary_logged_in_today'] = 'Logged In Today';

# Authentication
$lang['admin_auth_forgot_password_email'] = 'Email Address';
$lang['admin_auth_forgot_password_heading'] = 'Forgot Password';
$lang['admin_auth_login_heading'] = 'Login';
$lang['admin_auth_login_email'] = 'Email Address';
$lang['admin_auth_login_password'] = 'Password';
$lang['admin_auth_login_remember_me'] = 'Remember me';
$lang['admin_auth_login_button'] = 'Login';
$lang['admin_auth_login_fp'] = 'Forgot Password?';
$lang['admin_auth_reset_password_heading'] = 'Reset Password';
$lang['admin_auth_reset_password'] = 'Password';
$lang['admin_auth_reset_password_repeat'] = 'Repeat Password';
$lang['admin_auth_invalid_email_or_password'] = 'Invalid email or password';
$lang['admin_auth_inactive_account'] = 'Inactive Account';
# Calender
$lang['calendar_estimate'] = 'Estimate';
$lang['calendar_invoice'] = 'Invoice';
$lang['calendar_contract'] = 'Contract';
$lang['calendar_client_reminder'] = 'Client Reminder';
$lang['calendar_event'] = 'Event';
$lang['calendar_task'] = 'Task';
# Leads
$lang['lead_edit_delete_tooltip'] = 'Delete Lead';
$lang['lead_attachments'] = 'Attachments';
# Admin Customizer Sidebar
$lang['acs_finance'] = 'Finance';
# Settings
$lang['new_company_field_info'] = 'This field will be shown on invoices/estimates on the company side (left). You are not allowed to add any characters(dots,dashes,signs etc.) in the NAME field.';
$lang['new_company_field_name'] = 'Field Name';
$lang['new_company_field_value'] = 'Field Value';
$lang['new_company_field'] = 'Add New Company Field';
$lang['settings_number_padding_invoice_and_estimate'] = 'Invoice/Estimate Number Padding Zero\'s. <br /> <small>Ex. If this value is 3 the number will be formated: 005 or 025</small>';
$lang['settings_show_sale_agent_on_invoices'] = 'Show Sale Agent on Invoice';
$lang['settings_show_sale_agent_on_estimates'] = 'Show Sale Agent on Estimate';
$lang['settings_predefined_predefined_term'] = 'Predefined Terms & Conditions';
$lang['settings_predefined_clientnote'] = 'Predefined Client Note';
$lang['settings_custom_pdf_logo_image_url'] = 'Custom PDF Company Logo URL (JPG - 210x60px)';
$lang['settings_custom_pdf_logo_image_url_tooltip'] = 'Probably you will have problems with PNG images with transparency that are handled in different way depending on the php-imagick or php-gd version used. Try to update php-imagick and disable php-gd
. If you leave this field blank the uploaded logo will be used.';

# General
$lang['sale_agent_string'] = 'Sale Agent';
$lang['amount_display_in_base_currency'] = 'Amount is displayed in your base currency';

$lang['multiple_currencies_is_used_expenses_vs_income_report'] = 'Multiple currencies for invoices are used. The income amount wont be a hundred percents accurate.';
# Leads
$lang['leads_summary'] = 'Leads Summary';

# Contracts
$lang['contract_value'] = 'Contract Value';
$lang['contract_trash'] = 'Trash';
$lang['contracts_view_trash'] = 'View Trash';
$lang['contracts_view_all'] = 'All';
$lang['contracts_view_exclude_trashed'] = 'Exclude Trashed Contracts';
$lang['contract_value_tooltip'] = 'Add contract value. The value will be shown in your base currency.';
$lang['contract_trash_tooltip'] = 'If you add contract to trash, wont be shown on client side, wont be included in chart and other stats and also by default wont be shown when you will list all contracts.';

$lang['contract_renew_heading'] = 'Renew Contract';
$lang['contract_summary_heading'] = 'Contract Summary';
$lang['contract_summary_expired'] = 'Expired';
$lang['contract_summary_active'] = 'Active';
$lang['contract_summary_about_to_expire'] = 'About to Expire';
$lang['contract_summary_recently_added'] = 'Recently Added';
$lang['contract_summary_trash'] = 'Trash';
$lang['contract_summary_by_type'] = 'Contracts by Type';
$lang['contract_summary_by_type_value'] = 'Contracts Value by Type';
$lang['contract_renewed_successfuly'] = 'Contract renewed successfuly';
$lang['contract_renewed_fail'] = 'Problem while renewing the contract. Contact administrator';
$lang['no_contract_renewals_found'] = 'Renewals for this contracts is not found';
$lang['no_contract_renewals_history_heading'] = 'Contract Renewal History';
$lang['contract_renewed_by'] = '%s renewed this contract';
$lang['contract_renewal_deleted'] = 'Renewal successfuly deleted';
$lang['contract_renewal_delete_fail'] = 'Failed to delete contract renewal. Contact administrator';

$lang['contract_renewal_new_value'] = 'New Contract Value: %s';
$lang['contract_renewal_old_value'] = 'Old Contract Value: %s';

$lang['contract_renewal_new_start_date'] = 'New Start Date: %s';
$lang['contract_renewal_old_start_date'] = 'Old Contract Start Date was: %s';

$lang['contract_renewal_new_end_date'] = 'New End Date: %s';
$lang['contract_renewal_old_end_date'] = 'Old Contract End Date was: %s';
$lang['contract_attachment'] = 'Attachment';
$lang['contract_attachment_lowercase'] = 'attachment';

# Admin Aside Menu
$lang['als_goals_tracking'] = 'Goals Tracking';
$lang['als_expenses'] = 'Expenses';
$lang['als_reports_expenses'] = 'Expenses';
$lang['als_expenses_vs_income'] = 'Expenses vs Income';

# Invoices
$lang['invoice_attach_file'] = 'Attach File';
$lang['invoice_mark_as_sent'] = 'Mark as Sent';
$lang['invoice_marked_as_sent'] = 'Invoice marked as sent successfully';
$lang['invoice_marked_as_sent_failed'] = 'Failed to mark invoice as sent';

# Quick Actions
$lang['qa_new_goal'] = 'Setup New Goal';
$lang['qa_new_expense'] = 'Record Expense';

# Goals Tracking
$lang['goals'] = 'Goals';
$lang['goal'] = 'Goal';
$lang['goals_tracking'] = 'Goals Tracking';
$lang['new_goal'] = 'New Goal';
$lang['goal_lowercase'] = 'goal';
$lang['goal_start_date'] = 'Start Date';
$lang['goal_end_date'] = 'End Date';
$lang['goal_subject'] = 'Subject';
$lang['goal_description'] = 'Description';
$lang['goal_type'] = 'Goal Type';
$lang['goal_achievement'] = 'Achievement';
$lang['goal_contract_type'] = 'Contract Type';
$lang['goal_notify_when_fail'] = 'Notify staff members when goal failed to achieve';
$lang['goal_notify_when_achieve'] = 'Notify staff members when goal achieve';
$lang['goal_progress'] = 'Progress';
$lang['goal_total'] = 'Total: %s';
$lang['goal_result_heading'] = 'Goal Progress';
$lang['goal_income_shown_in_base_currency'] = 'Total income is shown in your base currency';
$lang['goal_notify_when_end_date_arrives'] = 'The staff members will be notified when the end date arrives.';
$lang['goal_staff_members_notified_about_achievement'] = 'The staff members are notified about this goal achievement';
$lang['goal_staff_members_notified_about_failure'] = 'Staff member are notified about the failure';
$lang['goal_notify_staff_manualy'] = 'Notify Staff Members Manualy';
$lang['goal_notify_staff_notified_manualy_success'] = 'The staff members are notified about this goal result';
$lang['goal_notify_staff_notified_manualy_fail'] = 'Failed to notify staff members about this goal result';

$lang['goal_achieved'] = 'Achieved';
$lang['goal_failed'] = 'Failed';
$lang['goal_close'] = 'Very Close';

$lang['goal_type_total_income'] = 'Achieve Total Income';
$lang['goal_type_convert_leads'] = 'Convert X Leads';
$lang['goal_type_increase_customers_without_leads_conversions'] = 'Increase Customer Number';
$lang['goal_type_increase_customers_without_leads_conversions_subtext'] = 'Leads Conversion is Excluded';

$lang['goal_type_increase_customers_with_leads_conversions'] = 'Increase Customer Number';
$lang['goal_type_increase_customers_with_leads_conversions_subtext'] = 'Leads Conversions is Included';
$lang['goal_type_make_contracts_by_type_calc_database'] = 'Make Contracts By Type';
$lang['goal_type_make_contracts_by_type_calc_database_subtext'] = 'Is calculated from the date added to database';
$lang['goal_type_make_contracts_by_type_calc_date'] = 'Make Contracts By Type';
$lang['goal_type_make_contracts_by_type_calc_date_subtext'] = 'Is calculated from the contract start date';
$lang['goal_type_total_estimates_converted'] = 'X Estimates Conversion ';
$lang['goal_type_total_estimates_converted_subtext'] = 'Will be taken only estimates who will be converted to invoices';
$lang['goal_type_income_subtext'] = 'Income will be calculated in your base currency (not converted)';
# Payments
$lang['payment_transaction_id'] = 'Transaction ID';
# Settings Menu
$lang['acs_expenses'] = 'Expenses';
$lang['acs_expense_categories'] = 'Expenses Categories';
# Expeneses
$lang['expense_category'] = 'Expense Category';
$lang['expense_category_lowercase'] = 'expense category';
$lang['new_expense'] = 'Record Expense';
$lang['expense_add_edit_name'] = 'Category Name';
$lang['expense_add_edit_description'] = 'Category Description';
$lang['expense_categories'] = 'Expense Categories';
$lang['new_expense_category'] = 'New Category';
$lang['dt_expense_description'] = 'Description';
$lang['expense'] = 'Expense';
$lang['expenses'] = 'Expenses';
$lang['expense_lowercase'] = 'expense';
$lang['expense_add_edit_tax'] = 'Tax';
$lang['expense_add_edit_customer'] = 'Customer';
$lang['expense_add_edit_currency'] = 'Currency';
$lang['expense_add_edit_note'] = 'Note';
$lang['expense_add_edit_date'] = 'Expense Date';
$lang['expense_add_edit_amount'] = 'Amount';
$lang['expense_add_edit_amount_tooltip'] = 'Amount will be displayed and calculated in your base currency';
$lang['expense_add_edit_billable'] = 'Billable';
$lang['expense_add_edit_attach_receipt'] = 'Attach Receipt';
$lang['expense_add_edit_reference_no'] = 'Reference #';
$lang['expense_receipt'] = 'Expense Receipt';
$lang['expense_receipt_lowercase'] = 'expense receipt';
$lang['expense_dt_table_heading_category'] = 'Category';
$lang['expense_dt_table_heading_amount'] = 'Amount';
$lang['expense_dt_table_heading_date'] = 'Date';
$lang['expense_dt_table_heading_reference_no'] = 'Reference #';
$lang['expense_dt_table_heading_customer'] = 'Customer';
$lang['expense_dt_table_heading_payment_mode'] = 'Payment Mode';
$lang['expense_converted_to_invoice'] = 'Expense successfuly converted to invoice';
$lang['expense_converted_to_invoice_fail'] = 'Failed to convert this expense to invoice check error log.';
$lang['expense_copy_success'] = 'The expense is copied successfuly.';
$lang['expense_copy_fail'] = 'Failed to copy expense. Please check the required fields and try again';
$lang['expenses_list_all'] = 'All';
$lang['expenses_list_tooltip'] = 'View Expenses';
$lang['expenses_list_billable'] = 'Billable';
$lang['expenses_list_non_billable'] = 'Non Billable';
$lang['expenses_list_invoiced'] = 'Invoiced';
$lang['expenses_list_unbilled'] = 'Unbilled';
$lang['expenses_list_recurring'] = 'Recurring';
$lang['expense_invoice_delete_not_allowed'] = 'You cant delete this expense. The expense is already invoiced.';
$lang['expense_convert_to_invoice'] = 'Convert To Invoice';
$lang['expense_edit'] = 'Edit Expense';
$lang['expense_delete'] = 'Delete';
$lang['expense_copy'] = 'Copy';
$lang['expense_invoice_not_created'] = 'Invoice Not Created';
$lang['expense_billed'] = 'Billed';
$lang['expense_not_billed'] = 'Not Billed';
$lang['expense_customer'] = 'Customer';
$lang['expense_note'] = 'Note:';
$lang['expense_date'] = 'Date:';
$lang['expense_ref_noe'] = 'Ref #:';
$lang['expense_tax'] = 'Tax:';
$lang['expense_amount'] = 'Amount:';
$lang['expense_recurring_indicator'] = 'Recurring';
$lang['expense_already_invoiced'] = 'This expense is already invoiced';
$lang['expense_recurring_auto_create_invoice'] = 'Auto Create Invoice';
$lang['expense_recurring_send_custom_on_renew'] = 'Send the invoice to customer email when expense repeated';
$lang['expense_recurring_autocreate_invoice_tooltip'] = 'If this option is checked the invoice for the customer will be auto created when the expense will be renewed.';
$lang['report_expenses_full'] = 'Full Report';
$lang['expenses_yearly_by_categories'] = 'Expenses Yearly by Categories';
$lang['total_expenses_for'] = 'Total Expenses for'; // year
$lang['expenses_report_for'] = 'Expenses for'; // year
$lang['expense_report_info'] = 'Billable expenses are not calculated in the report.';
# Custom fields
$lang['custom_field_required'] = 'Required';
$lang['custom_field_show_on_pdf'] = 'Show on PDF';
$lang['custom_field_leads'] = 'Leads';
$lang['custom_field_customers'] = 'Customers';
$lang['custom_field_staff'] = 'Staff';
$lang['custom_field_contracts'] = 'Contracts';
$lang['custom_field_tasks'] = 'Tasks';
$lang['custom_field_expenses'] = 'Expenses';
$lang['custom_field_invoice'] = 'Invoice';
$lang['custom_field_estimate'] = 'Estimate';
# Tickets
$lang['ticket_single_private_staff_notes'] = 'Private Staff Notes';


# Business News
$lang['business_news'] = 'Business News';

# Navigation
$lang['nav_todo_items'] = 'Todo items';
# Clients
# Contracts
$lang['clients_contracts_type'] = 'Contract Type';
# Home
$lang['exchange_rate_base_currency'] = 'Base Currency';
$lang['home_currency_exchange_rates'] = 'Currency Exchange Rates';

# Version 1.0.5
# General
$lang['no_tax'] = 'No Tax';
$lang['numbers_not_formated_while_editing'] = 'The rate in the input field is not formated while edit/add item and should remain not formated dont try to format it manually in here.';
# Contracts
$lang['contracts_view_expired'] = 'Expired';
$lang['contracts_view_without_dateend'] = 'Contracts Without Date End';

# Email Templates
$lang['email_template_contracts_fields_heading'] = 'Contracts';
# Invoices General
$lang['invoice_estimate_general_options'] = 'General Options';
$lang['invoice_table_item_description'] = 'Description';
$lang['invoice_recurring_indicator'] = 'Recurring';

# Estimates
$lang['estimate_convert_to_invoice_successfuly'] = 'Estimate converted to invoice successfuly';
$lang['estimate_table_item_description'] = 'Description';

# Version 1.0.6
# Invoices
# Currencies
$lang['cant_delete_base_currency'] = 'You cant delete the base currency. You need to assign new base currency the delete this.';
$lang['invoice_copy'] = 'Copy Invoice';
$lang['invoice_copy_success'] = 'Invoice copied successfuly';
$lang['invoice_copy_fail'] = 'Failed to copy invoice';
$lang['invoice_due_after_help'] = 'Set zero to avoid calculation';

$lang['show_shipping_on_invoice'] = 'Show shipping details in invoice';

# Estimates
$lang['show_shipping_on_estimate'] = 'Show shipping details in estimate';
$lang['is_invoiced_estimate_delete_error'] = 'This estimate is invoiced. You cant delete the estimate';

# Customers & Invoices / Estimates
$lang['ship_to'] = 'Ship to';
$lang['customer_profile_details'] = 'Customer Details';
$lang['billing_shipping'] = 'Billing & Shipping';
$lang['billing_address'] = 'Billing Address';
$lang['shipping_address'] = 'Shipping Address';

$lang['billing_street'] = 'Street';
$lang['billing_city'] = 'City';
$lang['billing_state'] = 'State';
$lang['billing_zip'] = 'Zip Code';
$lang['billing_country'] = 'Country';

$lang['shipping_street'] = 'Street';
$lang['shipping_city'] = 'City';
$lang['shipping_state'] = 'State';
$lang['shipping_zip'] = 'Zip Code';
$lang['shipping_country'] = 'Country';
$lang['get_shipping_from_customer_profile'] = 'Get shipping details from customer profile';

# Customer
$lang['customer_file_from'] = 'Showing from %s';
$lang['customer_default_currency'] = 'Default Currency';
$lang['customer_no_attachments_found'] = 'No attachments found';
$lang['customer_update_address_info_on_invoices'] = 'Update the shipping/billing info on all previous invoices/estimates';
$lang['customer_update_address_info_on_invoices_help'] = 'If you check this field shipping and billing info will be updated to all invoices and estimates. Note: Invoices with status paid wont be affected.';
$lang['setup_google_api_key_customer_map'] = 'Setup google api key in order to view to customer map';
$lang['customer_attachments_file'] = 'File';
$lang['client_send_set_password_email'] = 'Send SET password email';
$lang['customer_billing_same_as_profile'] = 'Same as Customer Info';
$lang['customer_billing_copy'] = 'Copy Billing Address';
$lang['customer_map'] = 'Map';
$lang['set_password_email_sent_to_client'] = 'Email to set password is successfuly sent to the client';
$lang['set_password_email_sent_to_client_and_profile_updated'] = 'Profile updated and email to set password is successfuly sent to the client';
$lang['customer_attachments'] = 'Files';
$lang['customer_longitude'] = 'Longitude (Google Maps)';
$lang['customer_latitude'] = 'Latitude (Google Maps)';

# Authentication
$lang['admin_auth_set_password'] = 'Password';
$lang['admin_auth_set_password_repeat'] = 'Repeat Password';
$lang['admin_auth_set_password_heading'] = 'Set Password';
$lang['password_set_email_subject'] = 'Set new password on %s';
# General
$lang['apply'] = 'Apply';
$lang['department_calendar_id'] = 'Google Calendar ID';
$lang['kan_ban_string'] = 'Kan Ban';
$lang['localization_default_language'] = 'Default Language';
$lang['system_default_string'] = 'System Default';
$lang['advanced_options'] = 'Advanced Options';
# Expenses
$lang['expense_list_invoice'] = 'Invoiced';
$lang['expense_list_billed'] = 'Billed';
$lang['expense_list_unbilled'] = 'Unbilled';
# Leads
$lang['lead_merge_custom_field'] = 'Merge as custom field';
$lang['lead_merge_custom_field_existing'] = 'Merge with existing database field';
$lang['lead_dont_merge_custom_field'] = 'Dont merge';
$lang['no_lead_notes_found'] = 'No lead notes found';
$lang['leads_view_list'] = 'List';
$lang['lost_leads'] = 'Lost Leads';
$lang['junk_leads'] = 'Junk Leads';
$lang['lead_mark_as_lost'] = 'Mark as lost';
$lang['lead_unmark_as_lost'] = 'Unmark Lead as lost';
$lang['lead_marked_as_lost'] = 'Lead marked as lost successfuly';
$lang['lead_unmarked_as_lost'] = 'Lead unmarked as lost successfuly';
$lang['leads_status_color'] = 'Color';

$lang['lead_mark_as_junk'] = 'Mark as junk';
$lang['lead_unmark_as_junk'] = 'Unmark Lead as junk';
$lang['lead_marked_as_junk'] = 'Lead marked as junk successfuly';
$lang['lead_unmarked_as_junk'] = 'Lead unmarked as junk successfuly';

$lang['lead_not_found'] = 'Lead Not Found';
$lang['lead_lost'] = 'Lost';
$lang['lead_junk'] = 'Junk';
$lang['leads_not_assigned'] = 'Not Assigned';
# Contacts
$lang['contract_not_visible_to_client'] = 'Hide from customer';
$lang['contract_edit_overview'] = 'Contract Overview';
$lang['contract_attachments'] = 'Attachments';
# Tasks
$lang['task_view_make_public'] = 'Make public';
$lang['task_is_private'] = 'Private Task';
$lang['task_is_private_help'] = 'This task is only visible to assignees,followers,creator and administrators';
$lang['task_finished'] = 'Finished';
$lang['task_single_related'] = 'Related';
$lang['task_unmark_as_complete'] = 'Unmark as complete';
$lang['task_unmarked_as_complete'] = 'Task unmarked as complete';
$lang['task_relation'] = 'Related';
$lang['task_public'] = 'Public';
$lang['task_public_help'] = 'If you set this task to public will be visible for all staff members. Otherwise will be only visible to members who are assignees,followers,creator or administrators';
# Settings
$lang['settings_general_favicon'] = 'Favicon';
$lang['settings_output_client_pdfs_from_admin_area_in_client_language'] = 'Output client PDF documents from admin area in client language';
$lang['settings_output_client_pdfs_from_admin_area_in_client_language_help'] = 'If this options is set to yes and ex. the system default language is english and client have setup language french the pdf documents will be outputed in the client language';
$lang['settings_cron_surveys'] = 'Surveys';
$lang['settings_default_tax'] = 'Default Tax';
$lang['setup_calendar_by_departments'] = 'Setup calendar by Departments';
$lang['settings_calendar'] = 'Calendar';
$lang['settings_sales_invoice_due_after'] = 'Invoice due after (days)';
$lang['settings_google_api'] = 'Google API Key';
$lang['settings_gcal_main_calendar_id'] = 'Google Calendar ID';
$lang['settings_gcal_main_calendar_id_help'] = 'This is the main company calendar. All events from this calendar will be shown. If you want to specify a calendar based on departments you can add in the department Google Calendar ID.';

$lang['show_on_calendar'] = 'Show on Calendar';
$lang['show_invoices_on_calendar'] = 'Invoices';
$lang['show_estimates_on_calendar'] = 'Estimates';
$lang['show_contracts_on_calendar'] = 'Contracts';
$lang['show_tasks_on_calendar'] = 'Tasks';
$lang['show_client_reminders_on_calendar'] = 'Client Reminders';

# Leads
$lang['copy_custom_fields_convert_to_customer'] = 'Copy custom fields to customer profile';
$lang['copy_custom_fields_convert_to_customer_help'] = 'If any of the following custom fields do not exists for customer will be auto created with the same name otherwise only the value will be copied from the lead profile.';
$lang['lead_profile'] = 'Profile';
$lang['lead_is_client'] = 'Client';
$lang['leads_kan_ban_notes_title'] = 'Notes';
$lang['leads_email_integration_folder_no_encryption'] = 'No Encryption';
$lang['leads_email_integration'] = 'Email Integration';
$lang['leads_email_active'] = 'Active';
$lang['leads_email_integration_imap'] = 'IMAP Server';
$lang['leads_email_integration_email'] = 'Email address (Login)';
$lang['leads_email_integration_password'] = 'Password';
$lang['leads_email_integration_port'] = 'Port';
$lang['leads_email_integration_default_source'] = 'Default Source';
$lang['leads_email_integration_check_every'] = 'Check Every (minutes)';
$lang['leads_email_integration_default_assigned'] = 'Responsibe for new lead';
$lang['leads_email_encryption'] = 'Encryption';
$lang['leads_email_integration_updated'] = 'Email Integration Updated';
$lang['leads_email_integration_default_status'] = 'Default Status';
$lang['leads_email_integration_folder'] = 'Folder';
$lang['leads_email_integration_notify_when_lead_imported'] = 'Notify when lead imported';
$lang['leads_email_integration_only_check_unseen_emails'] = 'Only check non opened emails';
$lang['leads_email_integration_only_check_unseen_emails_help'] = 'The script will auto set the email to opened after check. This is used to prevent checking all the emails again and again. Its not recomended to uncheck this option if you have a lot emails and you have setup a lot forwarding to the email you setup for leads';
$lang['leads_email_integration_notify_when_lead_contact_more_times'] = 'Notify if lead send email multiple times';
$lang['leads_email_integration_test_connection'] = 'Test IMAP Connection';
$lang['lead_email_connection_ok'] = 'IMAP Connection is good';
$lang['lead_email_connection_not_ok'] = 'IMAP Connection is not okey';
$lang['lead_email_activity'] = 'Email Activity';
$lang['leads_email_integration_notify_roles'] = 'Roles to Notify';
$lang['leads_email_integration_notify_staff'] = 'Staff Members to Notify';
$lang['lead_public'] = 'Public';
# Knowledge Base

$lang['kb_group_color'] = 'Color';
$lang['kb_group_order'] = 'Order';
# Utilities - BULK PDF Exporter
$lang['bulk_pdf_exporter'] = 'Bulk PDF Exporter';
$lang['bulk_export_pdf_payments'] = 'Payments';
$lang['bulk_export_pdf_estimates'] = 'Estimates';
$lang['bulk_export_pdf_invoices'] = 'Invoices';
$lang['bulk_pdf_export_button'] = 'Export';
$lang['bulk_pdf_export_select_type'] = 'Select Type';
$lang['no_data_found_bulk_pdf_export'] = 'No data found for export';
$lang['bulk_export_status_all'] = 'All';
$lang['bulk_export_status'] = 'Status';
$lang['bulk_export_zip_payment_modes'] = 'Made payments by';
$lang['bulk_export_include_tag'] = 'Include Tag';
$lang['bulk_export_include_tag_help'] = 'Ex. Original or Copy. The tag will be outputed in the PDF. Recomended to use only 1 tag';
# Predefined replies
$lang['no_predefined_replies_found'] = 'No predefined replies found';
## Clients area
$lang['clients_contract_attachments'] = 'Attachments';
# Backup
$lang['backup_type_full'] = 'Full Backup';
$lang['backup_type_db'] = 'Database Backup';

$lang['auto_backup_options_updated'] = 'Auto backup options updated';
$lang['auto_backup_every'] = 'Create backup every X days';
$lang['auto_backup_enabled'] = 'Enabled (Requires Cron)';
$lang['auto_backup'] = 'Auto backup';
$lang['backup_delete'] = 'Backup Deleted';
$lang['create_backup'] = 'Create Backup';
$lang['backup_success'] = 'Backup is made successfuly';
$lang['utility_backup'] = 'Database Backup';
$lang['utility_create_new_backup_db'] = 'Create Database Backup';
$lang['utility_backup_table_backupname'] = 'Backup';
$lang['utility_backup_table_backupsize'] = 'Backup size';
$lang['utility_backup_table_backupdate'] = 'Date';
$lang['utility_db_backup_note'] = 'Note: Due to the limited execution time and memory available to PHP, backing up very large databases may not be possible. If your database is very large you might need to backup directly from your SQL server via the command line, or have your server admin do it for you if you do not have root privileges.';

# Version 1.0.7
## Customers - portal
$lang['clients_nav_proposals'] = 'Proposals';
$lang['clients_nav_support'] = 'Support';
# General
$lang['more'] = 'More';
$lang['add_item'] = 'Add Item';
$lang['goto_admin_area'] = 'Go to admin area';
$lang['click_here_to_edit'] = 'Click here to edit';
$lang['delete'] = 'Delete %s';
$lang['welcome_top'] = 'Welcome %s';

# Customers
$lang['customer_permissions'] = 'Permissions';
$lang['customer_permission_invoice'] = 'Invoices';
$lang['customer_permission_estimate'] = 'Estimate';
$lang['customer_permission_proposal'] = 'Proposals';
$lang['customer_permission_contract'] = 'Contracts';
$lang['customer_permission_support'] = 'Support';


#Tasks
$lang['task_related_to'] = 'Related To';

# Send file
$lang['custom_file_fail_send'] = 'Failed to send file';
$lang['custom_file_success_send'] = 'The file is successfuly send to %s';
$lang['send_file_subject'] = 'Email Subject';
$lang['send_file_email'] = 'Email Address';
$lang['send_file_message'] = 'Message';
$lang['send_file'] = 'Send File';
$lang['add_checklist_item'] = 'Checklist Item';
$lang['task_checklist_items'] = 'Checklist Items';

# Import
$lang['default_pass_clients_import'] = 'Default Password for all customers';
$lang['simulate_import'] = 'Simulate Import';
$lang['import_upload_failed'] = 'Upload Failed';
$lang['import_total_imported'] = 'Total Imported: %s';
$lang['import_leads'] = 'Import Leads';
$lang['import_customers'] = 'Import Customers';
$lang['choose_csv_file'] = 'Choose CSV File';
$lang['import'] = 'Import';
$lang['lead_import_status'] = 'Status';
$lang['lead_import_source'] = 'Source';

# Bulk PDF Export
$lang['bulk_export_pdf_proposals'] = 'Proposals';

# Invoices
$lang['delete_invoice'] = 'Delete';

# Calendar
$lang['calendar_lead_reminder'] = 'Lead Reminder';

$lang['items'] = 'Items';
$lang['support'] = 'Support';
$lang['new_ticket'] = 'New Ticket';

# Reminders
$lang['client_edit_set_reminder_title'] = 'Add customer reminder';
$lang['lead_set_reminder_title'] = 'Add lead reminder';
$lang['set_reminder_tooltip'] = 'This option allows you to never forget anything about your customers.';
$lang['client_reminders_tab'] = 'Reminders';
$lang['leads_reminders_tab'] = 'Reminders';

# Tickets
$lang['delete_ticket_reply'] = 'Delete Reply';
$lang['ticket_priority_edit'] = 'Edit Priority';
$lang['ticket_priority_add'] = 'Add Priority';
$lang['ticket_status_edit'] = 'Edit Ticket Status';
$lang['ticket_service_edit'] = 'Edit Ticket Service';
$lang['edit_department'] = 'Edit Department';

# Expenses
$lang['edit_expense_category'] = 'Edit Expense Category';
# Settings
$lang['customer_default_country'] = 'Default Country';
$lang['settings_sales_require_client_logged_in_to_view_estimate'] = 'Require client to be logged in to view estimate';
$lang['set_reminder'] = 'Set Reminder';
$lang['set_reminder_date'] = 'Date to be notified';
$lang['reminder_description'] = 'Set description';
$lang['reminder_notify_me_by_email'] = 'Send also an email for this reminder';
$lang['reminder_added_successfuly'] = 'Reminder added successfuly. You will be notified in time.';
$lang['reminder_description'] = 'Description';
$lang['reminder_date'] = 'Date';
$lang['reminder_staff'] = 'Remind';
$lang['reminder_is_notified'] = 'Is notified?';
$lang['reminder_is_notified_boolean_no'] = 'No';
$lang['reminder_is_notified_boolean_yes'] = 'Yes';
$lang['reminder_set_to'] = 'Set reminder to';
$lang['reminder_deleted'] = 'Reminder deleted successfuly';
$lang['reminder_failed_to_delete'] = 'Failed to delete the reminder';
$lang['show_invoice_estimate_status_on_pdf'] = 'Show invoice/estimate status on PDF';
$lang['email_piping_default_priority'] = 'Default priority on piped ticket';
$lang['show_lead_reminders_on_calendar'] = 'Lead Reminders';
$lang['tickets_piping'] = 'Email Piping';
$lang['email_piping_only_replies'] = 'Only Replies Allowed by Email';
$lang['email_piping_only_registered'] = 'Pipe Only on Registed Users';
$lang['email_piping_enabled'] = 'Enabled';

# Estimates
$lang['view_estimate_as_client'] = 'View Estimate as Client';
$lang['estimate_mark_as'] = 'Mark as %s';
$lang['estimate_status_changed_success'] = 'Estimate status changed';
$lang['estimate_status_changed_fail'] = 'Failed to change estimate status';
$lang['estimate_email_link_text'] = 'View Estimate';

# Proposals
$lang['proposal_to'] = 'Company / Name';
$lang['proposal_date'] = 'Date';
$lang['proposal_address'] = 'Address';
$lang['proposal_phone'] = 'Phone';
$lang['proposal_email'] = 'Email';
$lang['proposal_date_created'] = 'Date Created';
$lang['proposal_open_till'] = 'Open Till';
$lang['proposal_status_open'] = 'Open';
$lang['proposal_status_accepted'] = 'Accepted';
$lang['proposal_status_declined'] = 'Declined';
$lang['proposal_status_sent'] = 'Sent';
$lang['proposal_expired'] = 'Expired';
$lang['proposal_subject'] = 'Subject';
$lang['proposal_total'] = 'Total';
$lang['proposal_status'] = 'Status';
$lang['proposals_list_all'] = 'All';
$lang['proposals_list_view'] = 'View Proposals';
$lang['proposals_not_related'] = 'Not Related';
$lang['proposals_leads_related'] = 'Leads Related';
$lang['proposals_customers_related'] = 'Customers Related';
$lang['proposal_related'] = 'Related';
$lang['proposal_for_lead'] = 'Lead';
$lang['proposal_for_customer'] = 'Customer';
$lang['proposal'] = 'Proposal';
$lang['proposal_lowercase'] = 'proposal';
$lang['proposals'] = 'Proposals';
$lang['proposals_lowercase'] = 'proposals';
$lang['new_proposal'] = 'New Proposal';
$lang['proposal_currency'] = 'Currency';
$lang['proposal_allow_comments'] = 'Allow Comments';
$lang['proposal_allow_comments_help'] = 'If you check this options comments will be allowed when your clients view the proposal.';
$lang['proposal_insert_items'] = 'Insert Items';
$lang['proposal_add_items'] = 'Add Items';
$lang['proposal_edit'] = 'Edit';
$lang['proposal_pdf'] = 'PDF';
$lang['proposal_send_to_email'] = 'Send to Email';
$lang['proposal_send_to_email_title'] = 'Send Proposal to Email';
$lang['proposal_attach_pdf'] = 'Attach PDF';
$lang['proposal_preview_template'] = 'Preview Template';
$lang['proposal_view'] = 'View Proposal';
$lang['proposal_copy'] = 'Copy';
$lang['proposal_delete'] = 'Delete';
$lang['proposal_mark_as_open'] = 'Mark as Open';
$lang['proposal_mark_as_declined'] = 'Mark as Declined';
$lang['proposal_mark_as_accepted'] = 'Mark as Accepted';
$lang['proposal_mark_as_sent'] = 'Mark as Sent';
$lang['proposal_to'] = 'To';
$lang['proposal_add_comment'] = 'Add Comment';
$lang['proposal_sent_to_email_success'] = 'Proposal sent to email successfuly';
$lang['proposal_sent_to_email_fail'] = 'Failed to sent proposal to email';
$lang['proposal_copy_fail'] = 'Failed to copy proposal';
$lang['proposal_copy_success'] = 'Proposal copied successfuly';
$lang['proposal_status_changed_success'] = 'Proposal status changed successfuly';
$lang['proposal_status_changed_fail'] = 'Failed to change proposal status';
$lang['proposal_assigned'] = 'Assigned';
$lang['proposal_comments'] = 'Comments';
$lang['proposal_convert'] = 'Convert';
$lang['proposal_convert_estimate'] = 'Estimate';
$lang['proposal_convert_invoice'] = 'Invoice';
$lang['proposal_convert_to_estimate'] = 'Convert to Estimate';
$lang['proposal_convert_to_invoice'] = 'Convert to Invoice';
$lang['proposal_convert_to_lead_disabled_help'] = 'You need to convert the lead to customer in order to create %s';
$lang['proposal_convert_not_related_help'] = 'The proposal needs to be related to customer in order to convert to %s';
$lang['proposal_converted_to_estimate_success'] = 'Proposal converted to estimate successfuly';
$lang['proposal_converted_to_invoice_success'] = 'Proposal converted to invoice successfuly';
$lang['proposal_converted_to_estimate_fail'] = 'Failed to convert proposal to estimate';
$lang['proposal_converted_to_invoice_fail'] = 'Failed to convert proposal to invoice';

# Proposals - view proposal template
$lang['proposal_total_info'] = 'Total %s';
$lang['proposal_accept_info'] = 'Accept';
$lang['proposal_decline_info'] = 'Decline';
$lang['proposal_pdf_info'] = 'PDF';

# Customers Portal
$lang['customer_reset_action'] = 'Reset';
$lang['customer_reset_password_heading'] = 'Reset your password';
$lang['customer_forgot_password_heading'] = 'Forgot Password';
$lang['customer_forgot_password'] = 'Forgot Password?';
$lang['customer_reset_password'] = 'Password';
$lang['customer_reset_password_repeat'] = 'Repeat Password';
$lang['customer_forgot_password_email'] = 'Email Address';
$lang['customer_forgot_password_submit'] = 'Submit';
$lang['customer_ticket_subject'] = 'Subject';

# Email templates
$lang['email_template_proposals_fields_heading'] = 'Proposals';

# Tasks
$lang['add_task_attachments'] = 'Attachment';
$lang['task_view_attachments'] = 'Attachments';
$lang['task_view_description'] = 'Description';
$lang['task_table_is_finished_indicator'] = 'Yes';
$lang['task_table_is_not_finished_indicator'] = 'No';
$lang['tasks_dt_finished'] = 'Finished';

# Affiliate Groups
$lang['affiliate_group_add_heading'] = 'Add New Affiliate Group';
$lang['affiliate_group_edit_heading'] = 'Edit Affiliate Group';
$lang['new_affiliate_group'] = 'New Affiliate Group';
$lang['affiliate_group_name'] = 'Name';
$lang['affiliate_groups'] = 'Groups';
$lang['affiliate_group'] = 'Affiliate Group';
$lang['affiliate_group_lowercase'] = 'affiliate group';

# Customer Groups
$lang['customer_group_add_heading'] = 'Add New Customer Group';
$lang['customer_group_edit_heading'] = 'Edit Customer Group';
$lang['new_customer_group'] = 'New Customer Group';
$lang['customer_group_name'] = 'Name';
$lang['customer_groups'] = 'Groups';
$lang['customer_group'] = 'Customer Group';
$lang['customer_group_lowercase'] = 'customer group';

$lang['customer_have_invoices_by'] = 'Contains invoices by status %s';
$lang['customer_have_estimates_by'] = 'Contains estimates by status %s';
$lang['customer_have_contracts_by_type'] = 'Having contracts by type %s';
$lang['customer_view_by'] = 'View Customers';

# Custom fields
$lang['custom_field_show_on_table'] = 'Show on table';
$lang['custom_field_show_on_client_portal'] = 'Show on client portal';
$lang['custom_field_show_on_client_portal_help'] = 'If this field is checked also will be shown in tables';
$lang['custom_field_visibility'] = 'Visibility';

# Utilities # Menu Builder
$lang['utilities_menu_translate_name_help'] = 'You can add here also translate strings. So if staff/system have language other then the default the menu item names will be outputed in the staff language. Otherwise if the string dont exists in the translate file will be taken the string you enter here.';
$lang['utilities_menu_icon'] = 'Icon';
$lang['active_menu_items'] = 'Active Menu Items';
$lang['inactive_menu_items'] = 'Inactive Menu Items';
$lang['utilities_menu_permission'] = 'Permission';
$lang['utilities_menu_url'] = 'URL';
$lang['utilities_menu_name'] = 'Name';
$lang['utilities_menu_save'] = 'Save Menu';

# Knowledge Base
$lang['view_articles_list'] = 'View Articles';
$lang['view_articles_list_all'] = 'All Articles';
$lang['als_add_article'] = 'Add Article';
$lang['als_all_articles'] = 'All Articles';
$lang['als_kb_groups'] = 'Groups';

# Customizer Menu
$lang['menu_builder'] = 'Menu Setup';
$lang['main_menu'] = 'Main Menu';
$lang['setup_menu'] = 'Setup Menu';
$lang['utilities_menu_url_help'] = '%s is auto appended to the url';

# Spam Filter - Tickets
$lang['spam_filters'] = 'Spam Filters';
$lang['spam_filter'] = 'Spam Filter';
$lang['new_spam_filter'] = 'New Spam Filter';
$lang['spam_filter_blocked_senders'] = 'Blocked Senders';
$lang['spam_filter_blocked_subjects'] = 'Blocked Subjects';
$lang['spam_filter_blocked_phrases'] = 'Blocked Phrases';
$lang['spam_filter_content'] = 'Content';
$lang['spamfilter_edit_heading'] = 'Edit Spam Filter';
$lang['spamfilter_add_heading'] = 'Add Spam Filter';
$lang['spamfilter_type'] = 'Type';
$lang['spamfilter_type_subject'] = 'Subject';
$lang['spamfilter_type_sender'] = 'Sender';
$lang['spamfilter_type_phrase'] = 'Phrase';

# Tickets
$lang['block_sender'] = 'Block Sender';
$lang['sender_blocked'] = 'Sender Blocked';
$lang['sender_blocked_successfuly'] = 'Sender Blocked Successfuly';
$lang['ticket_date_created'] = 'Date Created';

#KB
$lang['edit_kb_group'] = 'Edit group';
# Leads
$lang['edit_source'] = 'Edit Source';
$lang['edit_status'] = 'Edit Status';
# Contacts
$lang['contract_type_edit'] = 'Edit Contract Type';
# Reports
$lang['report_by_customer_groups'] = 'Total Value By Customer Groups';
#Utilities
$lang['ticket_pipe_log'] = 'Ticket Pipe Log';
$lang['ticket_pipe_name'] = 'From Name';
$lang['ticket_pipe_email_to'] = 'To';
$lang['ticket_pipe_email'] = 'From Email';
$lang['ticket_pipe_subject'] = 'Subject';
$lang['ticket_pipe_message'] = 'Message';
$lang['ticket_pipe_date'] = 'Date';
$lang['ticket_pipe_status'] = 'Status';

# Home
$lang['home_invoice_paid'] = 'Invoice Paid';
$lang['home_invoice_partialy_paid'] = 'Invoice Partially Paid';
$lang['home_estimate_declined'] = 'Estimate Declined';
$lang['home_estimate_accepted'] = 'Estimate Accepted';
$lang['home_estimate_sent'] = 'Estimate Sent';
$lang['home_latest_activity'] = 'Latest Activity';
$lang['home_my_tasks'] = 'My Tasks';
$lang['home_latest_activity'] = 'Latest Activity';
$lang['home_my_todo_items'] = 'My Todo Items';
$lang['home_widget_view_all'] = 'View All';
$lang['home_stats_full_report'] = 'Full Report';

# Validation - Customer Portal

$lang['form_validation_required']       = 'The {field} field is required.';
$lang['form_validation_valid_email']        = 'The {field} field must contain a valid email address.';
$lang['form_validation_matches']        = 'The {field} field does not match the {param} field.';
$lang['form_validation_is_unique']      = 'The {field} field must contain a unique value.';

$lang['footer_version'] = 'Version %s';
/* STOP TRANSLATING */



# DEPRECED - not used anymore
$lang['als_leads_new_lead_submenu'] = 'New Lead';
$lang['als_leads_view_lead_submenu'] = 'View Leads';
$lang['invoices_quick_info'] = 'Quick Invoices Info';
$lang['estimates_quick_info'] = 'Quick Estimates Info';
$lang['home_unfinished_invoices_not_sent'] = 'Invoice Not Sent';
$lang['home_unfinished_expired_estimates'] = 'Expired Estimates';
$lang['home_unfinished_invoice_overdue'] = 'Invoice Overdue';
$lang['home_unfinished_leads_contacted_today'] = 'Leads Contacted Today';
$lang['acs_sales'] = 'Sales';
$lang['acs_language_translator'] = 'Translator';
$lang['invoice_add_edit_new_item'] = 'New Item';
$lang['report_sales_type_items'] = 'Sales by Item';
$lang['reports_sales_dt_items_name'] = 'Item Name';
$lang['reports_sales_dt_items_quantity'] = 'Quantity';
$lang['reports_sales_dt_items_amount'] = 'Amount';
$lang['invoice_activity_modal_heading'] = 'Invoice Activity';
$lang['estimate_activity_modal_heading'] = 'Estimate Activity';
$lang['kb_folders'] = 'Folders';
$lang['new_language_name'] = 'New language name';
$lang['copy_language'] = 'Copy language';
$lang['copy_language_options'] = 'Copy';
$lang['delete_language_options'] = 'Delete';
$lang['set_default_language_options'] = 'Set Default';

$lang['client_edit_set_reminder'] = 'Set Reminder';
$lang['client_edit_set_reminder_date'] = 'Date to be notified';
$lang['client_edit_set_reminder_description'] = 'Set description';
$lang['client_edit_set_reminder_notify_me_by_email'] = 'Send also an email for this reminder';
$lang['client_reminder_added_successfuly'] = 'Reminder added successfuly. You will be notified in time.';
$lang['client_reminder_description'] = 'Description';
$lang['client_reminder_date'] = 'Date';
$lang['client_reminder_staff'] = 'Remind';
$lang['client_reminder_is_notified'] = 'Is notified?';
$lang['client_reminder_is_notified_boolean_no'] = 'No';
$lang['client_reminder_is_notified_boolean_yes'] = 'Yes';
$lang['client_reminder_set_to'] = 'Set reminder to';
$lang['client_reminder_deleted'] = 'Reminder deleted successfuly';
$lang['client_reminder_failed_to_delete'] = 'Failed to delete the reminder';
$lang['als_leads_estimates_submenu'] = 'Estimates';
$lang['als_leads_invoices_submenu'] = 'Invoices';
$lang['als_leads_payments_submenu'] = 'Payments';
$lang['als_leads_items_submenu'] = 'Items';
$lang['als_tickets'] = 'Tickets';
$lang['als_leads_add_article_submenu'] = 'Add Article';
$lang['als_leads_all_articles_submenu'] = 'All Articles';
$lang['als_leads_kb_groups_submenu'] = 'Groups';
$lang['home_leads_contacted_today'] = 'Leads Contacted Today';

?>
