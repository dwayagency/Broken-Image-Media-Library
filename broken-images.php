<?php
/**
* Plugin Name: Broken Images Scanner & Bulk Deleter
* Description: Scansiona la Libreria Media in batch (AJAX) per immagini che rispondono 404/410 o irraggiungibili, con filtro per date ed eliminazione massiva.
* Version: 1.3.0
* Author: DWAY Agency
* Text Domain: broken-images
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Broken_Images_Scanner {
	const NONCE_ACTION = 'broken_images_action';
	const OPTION_RESULTS = 'broken_images_last_results';

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin' ] );
		add_action( 'wp_ajax_broken_images_scan_step', [ $this, 'ajax_scan_step' ] );
		add_action( 'admin_post_broken_images_delete', [ $this, 'handle_delete' ] );
	}

	private function can_use() { 
		return current_user_can( 'upload_files' ); 
	}

	public function add_menu() {
		add_menu_page(
			__( 'Broken Images', 'broken-images' ),
			__( 'Broken Images', 'broken-images' ),
			'upload_files',
			'broken-images',
			[ $this, 'render_page' ],
			'dashicons-format-image',
			25
		);
	}

	public function enqueue_admin( $hook ) {
		if ( $hook !== 'toplevel_page_broken-images' ) return;

		// JS
		wp_enqueue_script( 'broken-images', plugin_dir_url(__FILE__) . 'broken-images.js', [ 'jquery' ], '1.3.0', true );
		wp_localize_script( 'broken-images', 'BROKEN_IMAGES', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( self::NONCE_ACTION ),
			'strings'  => [
				'starting' => __( 'Avvio scansione…', 'broken-images' ),
				'scanning' => __( 'Scansione in corso…', 'broken-images' ),
				'done'     => __( 'Scansione completata', 'broken-images' ),
				'error'    => __( 'Errore di comunicazione', 'broken-images' ),
			],
		] );

		// CSS per progress bar e tabella
		$css = '.bis-progress{margin:12px 0;height:16px;background:#eee;border-radius:8px;overflow:hidden;display:none}.bis-bar{height:100%;width:0;transition:width .25s;background:#2271b1}.bis-meta{margin-top:6px;color:#555}.bis-table{width:100%;margin-top:20px}.bis-table th{text-align:left;padding:8px}.bis-table td{padding:8px;border-bottom:1px solid #eee}#bis-cancel{display:none;margin-left:10px}';
		wp_add_inline_style( 'wp-admin', $css );
	}

	public function render_page() {
		if ( ! $this->can_use() ) { 
			wp_die( __( 'Non hai i permessi.', 'broken-images' ) ); 
		}
		
		$results = get_option( self::OPTION_RESULTS, [] );
		$broken  = isset( $results['broken'] ) ? (array) $results['broken'] : [];
		$checked = isset( $results['checked'] ) ? intval( $results['checked'] ) : 0;
		$ts      = isset( $results['timestamp'] ) ? intval( $results['timestamp'] ) : 0;
		$last    = $ts ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $ts ) : __( 'mai', 'broken-images' );
		
		$deleted = isset( $_GET['deleted'] ) ? intval( $_GET['deleted'] ) : 0;
		?>
		<div class="wrap">
			<h1><?php _e( 'Broken Images Scanner & Bulk Deleter', 'broken-images' ); ?></h1>
			<p><?php _e( 'Scansiona la Libreria Media per trovare immagini rotte (404/410/irraggiungibili).', 'broken-images' ); ?></p>

			<?php if ( $deleted > 0 ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php printf( __( '%d immagini eliminate con successo.', 'broken-images' ), $deleted ); ?></p>
				</div>
			<?php endif; ?>

			<div class="card" style="max-width:600px;padding:20px;margin-top:20px;">
				<h2><?php _e( 'Configura Scansione', 'broken-images' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th><label for="bis_from"><?php _e( 'Data inizio (opzionale)', 'broken-images' ); ?></label></th>
						<td><input type="date" id="bis_from" class="regular-text" /></td>
					</tr>
					<tr>
						<th><label for="bis_to"><?php _e( 'Data fine (opzionale)', 'broken-images' ); ?></label></th>
						<td><input type="date" id="bis_to" class="regular-text" /></td>
					</tr>
					<tr>
						<th><label for="bis_batch"><?php _e( 'Batch size', 'broken-images' ); ?></label></th>
						<td>
							<input type="number" id="bis_batch" value="100" min="10" max="500" class="small-text" />
							<p class="description"><?php _e( 'Immagini da controllare per richiesta (10-500)', 'broken-images' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="bis_limit"><?php _e( 'Limite massimo', 'broken-images' ); ?></label></th>
						<td>
							<input type="number" id="bis_limit" value="2000" min="100" max="10000" class="small-text" />
							<p class="description"><?php _e( 'Numero massimo di immagini da scansionare (100-10000)', 'broken-images' ); ?></p>
						</td>
					</tr>
				</table>

				<p>
					<button type="button" id="bis-start" class="button button-primary button-large">
						<?php _e( 'Avvia Scansione', 'broken-images' ); ?>
					</button>
					<button type="button" id="bis-cancel" class="button button-secondary">
						<?php _e( 'Annulla', 'broken-images' ); ?>
					</button>
				</p>

				<div class="bis-progress">
					<div class="bis-bar"></div>
				</div>
				<div id="bis-meta" class="bis-meta"></div>
			</div>

			<?php if ( ! empty( $broken ) ) : ?>
				<div class="card" style="margin-top:30px;padding:20px;">
					<h2><?php _e( 'Risultati Ultima Scansione', 'broken-images' ); ?></h2>
					<p>
						<strong><?php _e( 'Data scansione:', 'broken-images' ); ?></strong> <?php echo esc_html( $last ); ?><br>
						<strong><?php _e( 'Immagini controllate:', 'broken-images' ); ?></strong> <?php echo esc_html( $checked ); ?><br>
						<strong><?php _e( 'Immagini rotte trovate:', 'broken-images' ); ?></strong> <?php echo esc_html( count( $broken ) ); ?>
					</p>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php esc_attr_e( 'Sei sicuro di voler eliminare TUTTE le immagini rotte? Questa azione è irreversibile!', 'broken-images' ); ?>');">
						<input type="hidden" name="action" value="broken_images_delete" />
						<?php wp_nonce_field( self::NONCE_ACTION, '_wpnonce' ); ?>
						<button type="submit" class="button button-secondary">
							<?php printf( __( 'Elimina Tutte (%d)', 'broken-images' ), count( $broken ) ); ?>
						</button>
					</form>

					<table class="bis-table wp-list-table widefat fixed striped" style="margin-top:20px;">
						<thead>
							<tr>
								<th><?php _e( 'ID', 'broken-images' ); ?></th>
								<th><?php _e( 'Titolo', 'broken-images' ); ?></th>
								<th><?php _e( 'URL', 'broken-images' ); ?></th>
								<th><?php _e( 'Status', 'broken-images' ); ?></th>
								<th><?php _e( 'Azioni', 'broken-images' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $broken as $item ) : 
								$id     = isset( $item['id'] ) ? intval( $item['id'] ) : 0;
								$title  = get_the_title( $id );
								$url    = isset( $item['url'] ) ? esc_url( $item['url'] ) : '';
								$status = isset( $item['status'] ) ? esc_html( $item['status'] ) : 'N/A';
							?>
								<tr>
									<td><?php echo $id; ?></td>
									<td><?php echo esc_html( $title ); ?></td>
									<td><a href="<?php echo $url; ?>" target="_blank" rel="noopener"><?php echo esc_html( $url ); ?></a></td>
									<td><code><?php echo $status; ?></code></td>
									<td>
										<a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>" class="button button-small">
											<?php _e( 'Modifica', 'broken-images' ); ?>
										</a>
										<a href="<?php echo esc_url( get_delete_post_link( $id, '', true ) ); ?>" class="button button-small" onclick="return confirm('<?php esc_attr_e( 'Eliminare questa immagine?', 'broken-images' ); ?>');">
											<?php _e( 'Elimina', 'broken-images' ); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function ajax_scan_step() {
		check_ajax_referer( self::NONCE_ACTION, '_ajax_nonce' );
		
		if ( ! $this->can_use() ) {
			wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'broken-images' ) ] );
		}

		$from  = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
		$to    = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';
		$batch = isset( $_POST['batch'] ) ? max( 10, min( 500, intval( $_POST['batch'] ) ) ) : 100;
		$limit = isset( $_POST['limit'] ) ? max( 100, min( 10000, intval( $_POST['limit'] ) ) ) : 2000;
		$page  = isset( $_POST['page'] ) ? max( 1, intval( $_POST['page'] ) ) : 1;
		$checked_so_far = isset( $_POST['checked'] ) ? intval( $_POST['checked'] ) : 0;

		// Query args
		$args = [
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => $batch,
			'paged'          => $page,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		];

		// Filtro date
		if ( $from || $to ) {
			$args['date_query'] = [];
			if ( $from ) {
				$args['date_query'][] = [ 'after' => $from, 'inclusive' => true ];
			}
			if ( $to ) {
				$args['date_query'][] = [ 'before' => $to, 'inclusive' => true ];
			}
		}

		$query = new WP_Query( $args );
		$ids   = $query->posts;
		$max_pages = $query->max_num_pages;

		// Recupera risultati precedenti o inizializza
		$results = get_option( self::OPTION_RESULTS, [] );
		if ( $page === 1 ) {
			// Prima pagina: reset risultati
			$results = [
				'broken'    => [],
				'checked'   => 0,
				'timestamp' => time(),
			];
		}

		$broken = isset( $results['broken'] ) ? (array) $results['broken'] : [];

		// Controlla ogni immagine
		foreach ( $ids as $id ) {
			if ( $checked_so_far >= $limit ) {
				break; // Limite raggiunto
			}

			$url = wp_get_attachment_url( $id );
			if ( ! $url ) {
				continue;
			}

			$status = $this->check_image_url( $url );
			$checked_so_far++;

			// Se rotta, salva
			if ( $status !== 200 ) {
				$broken[] = [
					'id'     => $id,
					'url'    => $url,
					'status' => $status,
				];
			}
		}

		// Salva risultati aggiornati
		$results['broken']  = $broken;
		$results['checked'] = $checked_so_far;
		update_option( self::OPTION_RESULTS, $results );

		// Determina se finito
		$finished = ( $page >= $max_pages ) || ( $checked_so_far >= $limit ) || empty( $ids );

		wp_send_json_success( [
			'checked'      => $checked_so_far,
			'broken_count' => count( $broken ),
			'next_page'    => $page + 1,
			'max_pages'    => $max_pages,
			'finished'     => $finished,
		] );
	}

	private function check_image_url( $url ) {
		$response = wp_remote_head( $url, [
			'timeout'     => 10,
			'redirection' => 5,
			'sslverify'   => false,
		] );

		if ( is_wp_error( $response ) ) {
			return 'ERROR: ' . $response->get_error_message();
		}

		$code = wp_remote_retrieve_response_code( $response );
		return $code ? intval( $code ) : 'N/A';
	}

	public function handle_delete() {
		check_admin_referer( self::NONCE_ACTION, '_wpnonce' );
		
		if ( ! $this->can_use() ) {
			wp_die( __( 'Permessi insufficienti.', 'broken-images' ) );
		}

		$results = get_option( self::OPTION_RESULTS, [] );
		$broken  = isset( $results['broken'] ) ? (array) $results['broken'] : [];
		$deleted = 0;

		foreach ( $broken as $item ) {
			$id = isset( $item['id'] ) ? intval( $item['id'] ) : 0;
			if ( $id && wp_delete_attachment( $id, true ) ) {
				$deleted++;
			}
		}

		// Resetta risultati dopo eliminazione
		update_option( self::OPTION_RESULTS, [
			'broken'    => [],
			'checked'   => 0,
			'timestamp' => 0,
		] );

		wp_redirect( add_query_arg( 'deleted', $deleted, admin_url( 'admin.php?page=broken-images' ) ) );
		exit;
	}
}

// Inizializza plugin
new Broken_Images_Scanner();