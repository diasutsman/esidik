<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->output->enable_profiler(true);

        //$this->load->database();
        //$query = $this->db->query('select * from compte');

		// pallet_manager
		/*
        $query1 = $this->db->query('SELECT t0.username AS username_1, t0.username_canonical AS username_canonical_2, t0.email AS email_3, t0.email_canonical AS email_canonical_4, t0.enabled AS enabled_5, t0.salt AS salt_6, t0.password AS password_7, t0.last_login AS last_login_8, t0.confirmation_token AS confirmation_token_9, t0.password_requested_at AS password_requested_at_10, t0.roles AS roles_11, t0.id AS id_12, t0.nom AS nom_13 FROM compte t0 WHERE t0.id = 1 LIMIT 1');
        $query2 = $this->db->query('SELECT a0_.id AS id_0, a0_.date_depot_benne AS date_depot_benne_1, a0_.date_retour_benne AS date_retour_benne_2, a0_.date_reglement AS date_reglement_3, a0_.statut AS statut_4, p1_.id AS id_5, p1_.nom AS nom_6, p1_.adresse AS adresse_7, p1_.ville AS ville_8, p1_.code_postal AS code_postal_9, p1_.tel_portable AS tel_portable_10, p1_.tel_fixe AS tel_fixe_11, s2_.id AS id_12, s2_.nom AS nom_13, s2_.adresse AS adresse_14 FROM achat a0_ LEFT JOIN partenaire p1_ ON a0_.partenaire_id = p1_.id LEFT JOIN site s2_ ON a0_.site_id = s2_.id ORDER BY a0_.id DESC LIMIT 5');
        $query3 = $this->db->query('SELECT v0_.id AS id_0, v0_.date_vente AS date_vente_1, p1_.id AS id_2, p1_.nom AS nom_3, p1_.adresse AS adresse_4, p1_.ville AS ville_5, p1_.code_postal AS code_postal_6, p1_.tel_portable AS tel_portable_7, p1_.tel_fixe AS tel_fixe_8, s2_.id AS id_9, s2_.nom AS nom_10, s2_.adresse AS adresse_11 FROM vente v0_ LEFT JOIN partenaire p1_ ON v0_.partenaire_id = p1_.id LEFT JOIN site s2_ ON v0_.site_id = s2_.id ORDER BY v0_.id DESC LIMIT 5');
		
		*/
		


		$this->load->view('welcome_message');
	}
}
