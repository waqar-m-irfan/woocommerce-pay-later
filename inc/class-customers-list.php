<?php

class CustomersList extends WP_List_Table
{
    public function prepare_items()
    {

    }

    /**
     * Retrieve customer’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_customers($per_page = 5, $page_number = 1)
    {
        $args = array(
            'role' => 'customer',
//            'orderby' => 'user_nicename',
//            'order' => 'ASC'
        );
        $users = get_users($args);

        var_dump($users);

//        echo '<ul>';
//        foreach ($users as $user) {
//            echo '<li>' . esc_html($user->display_name) . '[' . esc_html($user->user_email) . ']</li>';
//        }
//        echo '</ul>';
    }

    /**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'address':
			case 'city':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}
}