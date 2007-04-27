<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author James Williams <james.l.williams@gmail.com>
 * @author Christophe Foiret <christophe.foiret@valdoise.fr>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go ne marche pas avec la configuration actuelle de la serveur.<BR>Votre version PHP est <I>%s</I>. Les besoins minimaux du framework sont <I>%s</I>.<BR>Installer une version �gale ou sup�rieure pour un fonctionnement normal";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "L'extension <I>%s</I> n'est pas support�e par l'installation PHP actuelle.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Erreur dans la classe %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Erreur dans le script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Error dans la base de donn�es";
$P2G_LANGUAGE['ERR_INVALID_VALUE'] = "Valeur invalide!";
$P2G_LANGUAGE['ERR_DUPLICATED_VALUE'] = "Cette valeur �tait d�j� ins�r�e!";
$P2G_LANGUAGE['ERR_COMM_FAILURE'] = "Echec de communication";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Impossible de charger les param�tres de la connexion de base de donn�es! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "Les param�tres de connexion � la base sont invalides! Les entr�es USER, BASE et TYPE sont obligatoire. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Ce n'�tait pas possible � connecter � la base de donn�es avec la configuration d'acc�s actuelle. Sont-ils correctes?";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS_PATH'] = "Impossible d'importer et/ou cr�er une instance de la classe de connexion %s!";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS'] = "La classe de connexion %s n'est pas une sous-classe de php2go.db.Db!";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Erreur fatale",
	2 => "Alerte",
	4 => "Erreur syntaxe",
	8 => "Avis",
	16 => "Erreur fatale du noyau",
	32 => "Alerte du noyau",
	64 => "Fatal Error du compilateur",
	128 => "Alerte du compilateur",
	256 => "Erreur d'usager",
	512 => "Alerte d'usager",
	1024 => "Avis d'usager"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "L'entr�e 'ABSOLUTE_URI' n'�tait pas fix�e dans la tableau global des configurations!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "L'entr�e <I>%s</I> n'�tait pas fix�e dans le tableau global des configurations!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "Le fichier de messages pour la domaine %s, langage %s, est invalide ou n'existe pas.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Impossible de trouver l'entr�e <I>%s</I> dans la tableau global des configurations. Etait-il fix� correctement dans l'installation?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Impossible de charger le module <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Impossible de charger les modules d'un dossier valide � <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Impossible de charger le fichier <I>%s</I> � <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Impossible de convertir le date � la forme %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "La classe %s ne marche pas sur Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "La fonction %s n'existe pas!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "La m�thode %s n'est pas d�finie dans la classe %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "La classe <I>%s</I> est abstraite et vous ne pouvez pas cr�er un objet de cette classe!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "La date %s n'est pas sous la forme requise %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "La variable %s envoy�e � la fonction ou m�thode %s n'est pas une ressource valide.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "L'authenticator %s n'est pas une sous-classe de php2go.auth.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Impossible d'importer et/ou cr�er une instance du authenticator %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "Le conteneur d'utilisateur %s n'est pas une sous-classe de php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH' ] = "Ce n'�tait pas possible d'importer et/ou cr�er un instance du conteneur d'utilisateur %s!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER'] = "L'autoriseur %s n'est pas une sous-classe de php2go.auth.Authorizer!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER_PATH'] = "Impossible d'importer et/ou cr�er un instance de l'autoriseur %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Erruer en analysant la fichier XML:<BR>Erreur: %s<BR>Ligne: %s, Colonne: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Erreur en analysant le code PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Impossible de trouver le fichier ou dossier %s. Existe-t-il ?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Impossible d'ouvrir le fichier %s avec le mode %s. V�rifier les permissions entr�es/sorties et the used mode.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Impossible de cr�er le fichier ou dossier %s. V�rifier les permissions entr�es/sorties dans la dossier parent.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Impossible d'ouvrir le fichier %s pour lire. Existe-t-il? Est-ce que le chemin est correct?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Impossible d'ouvrir le fichier ou dossier %s pour �crire. Avez-vous la permission de l'�craser ?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Impossible de detruire le fichier %s.<BR>Avez-vous la permission d'�crire dans ce dossier? Est-ce que le fichier a la permission d'�tre supprim� ?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Impossible d'ouvrir le fichier %s pour inclure. Existe-t-il? Est-ce que le chemin est correct ?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Impossible d'appliquer le mode %s au fichier ou dossier %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Impossible de trouver le dossier %s. Existe-t-il?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Impossible d'ouvrir le dossier %s pour lire. V�rifier ses permissions.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Impossible d'ouvrir le fichier contenant l'objet s�rialis� pour lire. Est-ce que le chemin est correct? Le fichier est-il cr��?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "La requ�te ins�r�e dans la table '%s' a retourn� un set vide";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "La requ�te actualis�e dans la table '%s' avec la clause %s a retourn� un set vide";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "La requ�te %s, utilis�e dans publier les variables dans the global scope, a retourn� un set vide.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "La requ�te pass�e � la m�thode Db::toGlobals() doit commencer avec 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Impossible d'initialiser le gabarit avec un nom de fichier vide!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "Le gabarit n'�tait pas pr�par�, parce que son contenu est vide. V�rifier le contenu pass� au constructeur.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Impossible de trouver la variable %s dans le gabarit <I>%s</I>. <BR>Recevoir des r�sultats corrects de cet outil, ins�rez {%s} dans le code du gabarit.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Impossible de trouver le bloc <I>%s</I> pour cr�ation. Est-il declar� dans le gabarit?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Impossible de trouver le bloc %s de r�utiliser comme %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "L'�l�ment %s �tait d�j� configur� avec la m�thode DocumentElement::parse(). Impossible d'ajouter des �l�ments avec la m�thode put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "Le gabarit %s n'�tait pas pr�par�. G�rez la m�thode %s::parse(); avant et ex�cutez les op�rations avec l'objet.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Ce gabarit �tait d�j� pr�par�. Vous devez appeler la m�thode parse() une seule fois.";
$P2G_LANGUAGE['ERR_TPLPARSE_DEFINED_BLOCK'] = "Le bloc %s �tait d�j� d�fini!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_BLOCKDEF'] = "La d�finition du bloc <I>%s</I> est d�s�quilibr�e!";
$P2G_LANGUAGE['ERR_TPLPARSE_INCOMPLETE_BLOCKDEF'] = "La sp�cification du bloc <I>%s</I> est incomplet � l'int�rieur du tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_BLOCKINSIDELOOP'] = "Impossible de d�finir des blocs dynamiques � l'int�rieur de boucles!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_TAG'] = "La d�finition du tag <I>%s</I> est d�s�quilibr�e!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNEXPECTED_TAG'] = "Tag inattendu <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_PARENTHESIS'] = "Parenth�se d�s�quilibr�e  � l'int�rieur de la formule <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_TAG_SYNTAX'] = "La d�finition du tag <I>%s</I> est invalide : %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TOKEN'] = "Token invalide � l'int�rieur de la formule <I>%s</I> : %s";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTE'] = "Manqu� ou invalide valeur de nom de l'attribut!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTENAME'] = "<I>%s</I> n'est pas un nom d'attribut valide!";
$P2G_LANGUAGE['ERR_TPLPARSE_REQUIRED_ATTRIBUTE'] = "La propri�t� <I>%s</I> est requise dans la d�finition du tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ATTRIBUTE'] = "La propri�t� <I>%s</I> est invalide dans la d�finition du tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_ASSIGN'] = "La caract�re '=' n'�tait pas trouv� apr�s le nom de l'attribut!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_IS_EXPR'] = "L'op�ration est invalide pour une op�ration 'is' : <I>%s</I>";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_TAG_ARGS'] = "Le tag <I>%s</I> doit avoir des arguments!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ARGS'] = "Le tag <I>%s</I> ne peut pas contenir des arguments!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_MODIFIER'] = "Le modificateur <I>%s</I> est invalide ou n'existe pas!";
$P2G_LANGUAGE['ERR_WIDGET_INCLUDE'] = "C'est possible d'utiliser le widget <i>%s</i> seulement entre les marqueurs INCLUDE WIDGET!";
$P2G_LANGUAGE['ERR_WIDGET_MANDATORY_PROPERTY'] = "La propri�t� <i>%s</i> est requise dans la d�finition du widget <i>%s</i>!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "Il n'y a pas d'�l�ments d�finis dans la plan de document fourni.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "Le bloc de base d'un gabarit ne peut pas �tre �cras�.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "La valeur de %s doit �tre sup�rieure � z�ro dans la fonction %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "La valeur de %s doit �tre un tableau de la fonction %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "La valeur de '%s' est out of the allowed bounds: %s and %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "La valeur de <I>%s</I> n'est pas une URL valide!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> n'est pas une valide h�te de connexion!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Impossible de trouver une DNS valide au domaine %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "La propri�t� %s de la variable de session %s est absente. Ex�cutez la fonction createProperty() ";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "Le minuteur %s de la variable de la session %s est absent. Ex�cutez la fonction createTimeCounter() ";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "L'object pass� comme param�tre n'est pas un instance valide de la classe <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Param�tres insuffisants pour construire la calcultrice!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Param�tres insuffisants pour construire la s�lection de la couleur!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "La m�thode %s n'est pas valide pour soumettre le formulaire '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> n'est pas un nom valide pour une fonction de validation !";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "Le champ <I>%s</I> est d�j� d�fini dans le formulaire <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "Le bouton <I>%s</I> existe d�j� dans le formulaire <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "La section ID <I>%s</I> �tait d�j� d�finie dans le formulaire <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Impossible de visualiser la section <I>%s</I>, parce que la fonction <I>%s</I> n'est pas d�finie!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "La section <I>%s</I> est conditionnelle. Vouz devez d�clarer un bloc nomm� <I>%s</I> dans le gabarit contenu dans la section champs!";
$P2G_LANGUAGE['ERR_FORM_REQUIRED_SUMMARY'] = "Les champs suivants du formualaire sont requis :";
$P2G_LANGUAGE['ERR_FORM_ERRORS_SUMMARY'] = "Ce formulaire contient les erreurs suivantes :";
$P2G_LANGUAGE['ERR_FORM_COMPLETE_FIELDS'] = "S'il vous pla�t, compl�tez les champs et essayez encore.";
$P2G_LANGUAGE['ERR_FORM_FIX_FIELDS'] = "S'il vous pla�t, corrigez les champs et essayez encore.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "Le champ %s est obligatoire.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID'] = "Le champ %s a une valeur incorrecte!";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_DATATYPE'] = "Le champ %s contient une valeur invalide %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "La pr�cision num�rique du champ %s est invalide.\nLes tailles maximales sont %s pour la partie enti�re et %s pour la partie d�cimale.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "Le champ %s ne doit contenir que des caract�res alphanum�riques.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "Le champ %s ne doit contenir que les valeurs suivantes : <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "Le champ %s contient un num�ro de carte de cr�dit invalide %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "Le champ %s doit avoir au moins %s caract�res.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "Le champ %s ne contient que %s caract�res.";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "Le champ %s doit �tre �gal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "Le champ %s ne peut pas �gal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "Le champ %s doit �tre sup�rieur au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "Le champ %s doit �tre inf�rieur au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "Le champ %s doit �tre sup�rieur ou �gal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "Le champ %s doit �tre inf�rieur ou �gal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "Le champ %s doit �tre �gal � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "Le champ %s ne peut �tre �gal � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "Le champ %s doit �tre sup�rieur � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "Le champ %s doit �tre inf�rieur � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "Le champ %s doit �tre sup�rieur ou �gal � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "Le champ %s doit �tre inf�rieur ou �gal � %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "Le masque <I>%s</I>, sur le champ <I>%s</I> n'est pas valide!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "Le champ type <I>%s</I> n'est pas valide!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "Le code saisi dans le champ %s ne correspond pas au contenu de l'image!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "Le champ type <I>%s</I> ne peut pas �tre utilis� dans un <I>grid</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "La valeur de l'attribut <I>CELLSIZES</I> dans le champ <I>%s</I> est invalide. Vous devez fournir N+1 tailles (N=FIELDSET size) et la somme des tailles doit �tre �gal � 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "La requ�te d�finie dans le <I>DATASOURCE</I> du champ <I>%s</I> doit retourner N+2 colonnes (N=FIELDSET size)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Les attributs du contr�leur d'�v�nements <I>%s</I> sont invalides ou incomplets!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Les attributs de la r�gle <I>%s</I> sont invalides ou incomplets!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "La sp�cification du marqueur DATASOURCE dans le champ <I>%s</I> est invalide.<BR>Les noeuds enfants KEYFIELD et LOOKUPTABLE sont obligatoires!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "La sp�cification du champ <I>%s</I> est incomplet. Vous devez fournir au moins un noeud enfant &lt;OPTION&gt; !";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "L'attribut VALUE de l'option ou indice <I>%d</I> dans le champ <I>%s</I> est manquant!";
$P2G_LANGUAGE['ERR_MISSING_DBGROUPFIELD_DATASOURCE'] = "Il n'�tait pas d�fini un marqueur DATASOURCE tag au %s <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBGROUPFIELD_RESULTS'] = "La requ�te fournie dans l'�l�ment DATASOURCE %s <I>%s</I> a retourn� un set vide!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "L'attribut VALUE de l'option � indice %d dans le COMBOFIELD <I>%s</I> est manquant!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "La sp�cification du EDITSELECTIONFIELD <I>%s</I> est incompl�te. Les noeuds enfants EDITFIELD et LOOKUPFIELD sont obligatoires!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "La sp�cification du LOOKUPSELECTIONFIELD <I>%s</I> est erronn�e. Le noeud doit avoir deux enfants LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "Il n'y a aucun DATASOURCE d�fini dans le premier enfant LOOKUPFIELD du LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Les items \$fields et \$tables sont obligatoires dans la construction de la r�quete SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "La structure de la grille <I>%s</I> est invalide. Vouz devez d�clarer un <I>DATASOURCE</I> et un <I>FIELDSET</I> doit contenir au moins un champ!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "La sp�cification du RANGEFIELD <I>%s</I> est erronn�e. Le noeud doit avoir deux enfants EDITFIELD, COMBOFIELD, LOOKUPFIELD ou DATEPICKERFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "La sp�cification du DATAFILTER <I>%s</I> est invalide.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "Le masque assign� au DATAFILTER <I>%s</I> est invalide.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Il existe d�j� un autre DATAFILTER avec ID <I>%s</I>.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "La sp�cification du EDITSEARCHFIELD <I>%s</I> est incompl�te. Vous devez fournir au moins un DATAFILTER, un DATASOURCE et un LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Impossible de compl�ter l'op�ration!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "Recherche impossible car tous les champs de recherche sont vides.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "Recherche impossible car les valeurs de filtres sont vides ou plus courtes que %d carat�res.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "Le nombre de colonnes group�es doit �tre inf�rieur au nombre de colonnes!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "La colonne <I>%s</I> n'est pas d�finie dans la r�sultat de la requ�te!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "La variable <I>%s</I> du bloc <I>%s</I> n'�tait pas declar�e dans le gabarit!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "La valeur <I>%s</I> n'est pas une d�finition valide aux tailles des colonnes!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "La somme des tailles de colonnes pass�e � la fonction setColumnSizes doit �tre �gale � 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "Le nombre de colonnes fourni (%s) n'est pas �gal au nombre de colonnes du report (%s, %s grouping)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Impossible de consruire le formulaire de recherche, parce que la variable <I>%s</I> est introuvable. <BR>D�clarez {%s} dans le gabarit du document.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Vous devez fournir le Label, le Field Name et la Mask dans la construction des param�tres de recherche!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "Le masque <I>%s</I> du param�tre de recherche <I>#%d</I> est invalide!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "La fonction setAlternateStyle doit recevoir au moins deux param�tres (CSS names)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Une colonne cach�e (%s) ne peut pas �tre incluse dans les titres de groupe!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Une colonne affich�e (%s) ne peut pas �tre d�finie comme cach�e!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "La nombre de colonnes cach�es doit �tre plus petit que le nombre de colonnes de la requ�te!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "La colonne <I>%s</I> qui est d�finie comme cach�e n'existe pas dans le result set de la requ�te!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "Le type <I>%s</I> n'est pas un type valide de DataSet!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "La requ�te pass�e dans le param�tre \$rootSql de la m�thode Menu::loadFromDatabase() a retourn� un set vide ou n'a pas les champs requis et minumums (CAPTION and LINK)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "La requ�te pass�e dans le param�tre \$childSql de la m�thode Menu::loadFromDatabase() est invalide.<BR>Fournissez une expression qui contient le '~var~' (Relation Column) plus les deux champs requis (CAPTION and LINK)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "Le menu n'�tait pas cr�e. Construisez les items du menu en utilisant Menu::loadFromDatabase() ou Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "Le noeud racine du fichier XML n'a pas d'enfant!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "L'image <I>%s</I> n'est pas valide. Les valeurs possibles sont: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Impossible d'ouvrir la connexion. Vous devez �tablir la connexion en utilisant les m�thodes FtpClient::setServer() et FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Authentification impossible. Donn�es absentes.";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "La limite de spreadsheet est d�pass�e : [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "La taille maximum pour un commentaire sur la cellule est d�pass�<BR>Valeur: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "La maximum de quatre polices pour chaque spreadsheet est d�pass�!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Impossible de compl�ter le t�l�chargement. Le fichier n'a pas �t� transf�r� correctement.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Impossible de compl�ter le t�l�chargement. La taille du fichier est sup�rieure � la taille maximum permise.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "le t�l�chargement de compl�ter la transmission de la fichier. Le nom de fichier %s est invalide.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "le t�l�chargement de compl�ter la transmission de la fichier. L'extension %s n'est pas permise.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Ce n'�tait pas possible de compl�ter la transmission de la fichier. La fichier <I>%s</I> existe d�j� et n'est pas �cras�.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Impossible de d�placer le fichier temporaire dans le dossier destinaire.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Impossible d'ouvrir le socket utilisant le port %s avec l'h�te %s!%s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "La connexion avec le socket n'est pas active!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Fin de fichier attendu, l'op�ration ne peut pas �tre accomplie!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "La temporisation socket �tait d�pass�e. L'op�ration ne peut pas �tre accomplie!";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Il n'y a aucun destinataire pour le message!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Impossible de signer le contenu du message.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Erreur en se connectant au serveur SMTP %s!<BR>[Erreur %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Erreur en ex�cutant la commande %s sur le serveur SMTP!<BR>[Erreur %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Authentification impossible avec le serveur SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Pour crypter ou d�crypter les donn�es, vous devez fournir une cl� avec la m�thode Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Impossible d'initialiser le cryptogramme <I>%s</I> en utilisant le mode %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Impossible d'extraire une cl� publique et de crypter les donn�es. Fournissez un fichier contenant un certificat valide � la m�thode OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Impossible d'extraire une cl� publique et de v�rifier la signature incluse dans ces donn�es. Fournissez un fichier contenant un certificat valide � la m�thode OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Impossible d'extraire une cl� priv�e et de d�crypter les donn�es. Fournissez un fichier contenant la cl� priv�e et la phrase de passe, si la cl� en a besoin.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Impossible d'extraire une cl� priv�e pour g�n�rer la signature num�rique. Fournissez un fichier contenant une cl� priv�e et la phrase de passe, si la cl� en a besoin.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "Le chemin d'acc�s du certificat est invalide!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Impossible de lire le contenu du certificat %s!";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Erreur en se connectant au serveur POP3 %s!<BR>[Erreur %d] - %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Erreur en ex�cutant la commande %s sur le serveur POP!<BR>%s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Authentification impossible avec le serveur POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Impossible de g�rer la commande APOP : il n'y a pas de banni�re d'authentification !";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Impossible d'�tablir une connexion HTTP avec l'h�te <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "L'h�te HTTP n'�tait pas fix�. G�rez la m�thode HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Impossible de cr�er une instance de la classe de validation <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "La classe de validation <I>%s</I> est invalide! La m�thode %s::execute() ne peut pas �tre trouv�e.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "L'expression �valu� n'a pas donn� le r�sultat pr�vu.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "La fonction ou m�thode <I>%s</I> n'est pas une callback valide!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "La fichier <I>%s</I> ne contient aucune donn�e de configuration valide!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> n'est past une sp�cification valide de couleur.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Impossible d'attribuer une nouvelle couleur pour cette image!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Il y a un nom d'handler JSRS ou alias manquant!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "Le handler JSRS %s est invalide!";
$P2G_LANGUAGE['ERR_AJAX_SUPPORT'] = "Votre navigateur web ne peut pas fonctionner en AJAX!";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPNAME'] = "JSON Decode Error: %s n'est pas un valide nom de l'attribut";
$P2G_LANGUAGE['ERR_JSON_MISSING_COLON'] = "JSON Decode Error: deux points manquants � la position %d";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPVALUE'] = "JSON Decode Error: %s n'est pas une valide valeur de l'attribut";
$P2G_LANGUAGE['ERR_JSON_MISSING_COMMA'] = "JSON Decode Error: virgule manquante � la position %d";
$P2G_LANGUAGE['ERR_JSON_ESCAPE_SEQUENCE'] = "JSON Decode Error: invalide s�quence trouv�e � la position %d";
$P2G_LANGUAGE['ERR_JSON_UNTERMINATED_STRING'] = "JSON Decode Error: string inachev�e trouv�e � %d";
$P2G_LANGUAGE['ERR_JSON_CYCLE'] = "L'encodeur JSON a trouv� un cycle dans un instance de la classe %s!";
$P2G_LANGUAGE['ERR_JSON_ENCODE'] = "Une erreur �tait trouv� en convertissant le type %s �  un string JSON!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Soumettre";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Ouvrir le calendrier";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Ouvrir la calculatrice";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "La Calculatrice";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "R�sultat";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Janvier", "F�vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao�t", "Septembre", "Octobre", "Novembre", "D�cembre");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Jan", "F�v", "Mar", "Avr", "Mai", "Juin", "Juil", "Ao�t", "Sep", "Oct", "Nov", "D�c");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Uses %s Compression -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Taille originale: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Taille packed: %d";
$P2G_LANGUAGE['EMPTY_SEARCH'] = 'Vous devez choisir un filtre et entrer un terme de recherche!!';
$P2G_LANGUAGE['EMPTY_RESULTS'] = 'La recherche a retourn� un r�sultat vide!';
$P2G_LANGUAGE['FORM_INPUT_MASK_MESSAGES'] = array(
	'optionalCharFirst' => "L'indicateur faculatif doit suivre un litt�ral.",
	'escapeCharLast' => "L'indicateur �chappatoire doit pr�c�der un litt�ral.",
	'nestedCharClass' => "La classe du caract�re est invalide.",
	'invalidLimits' => "La d�finition de la grandeur est invalide."
);
$P2G_LANGUAGE['FORM_MASKS'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'Devise',
	'DATE' => 'Date',
	'TIME' => 'L\'heure',
	'EMAIL' => 'Courriel',
	'FLOAT' => 'Nombre Flottant',
	'INTEGER' => 'Nombre Entier',
	'DIGIT' => 'Nombre',
	'IP' => 'Adresse IP',
	'WORD' => 'Texte alphanum�rique',
	'URL' => 'URL/Adresse internet',
	'ZIP' => 'Code Postal'
);
$P2G_LANGUAGE['EDITSEARCH_BTN_VALUE'] = 'Rechercher...';
$P2G_LANGUAGE['EDITOR_VARS'] = array(
	'formatBlock' => 'Paragraphe',
	'formatBlockOptions' => array(
		'h1' => 'Titre 1 &lt;H1&gt;',
		'h2' => 'Titre 2 &lt;H2&gt;',
		'h3' => 'Titre 3 &lt;H3&gt;',
		'h4' => 'Titre 4 &lt;H4&gt;',
		'h5' => 'Titre 5 &lt;H5&gt;',
		'h6' => 'Titre 6 &lt;H6&gt;',
		'address' => 'Addresse &lt;ADDR&gt;',
		'pre' => 'Format� &lt;PRE&gt;',
		'removeformat' => 'Enlever Format'
	),
	'topButtons' => array(
		'bold' => 'Format en gras',
		'italic' => 'Format Italique',
		'underline' => 'Format Soulign�',
		'strikethrough' => 'Formater comme texte barr�',
		'cut' => 'Couper',
		'copy' => 'Copier',
		'paste' => 'Coller',
		'pickforecolor' => 'Changer la couleur d\'avant-plan',
		'pickbackcolor' => 'Changer la couleur d\'arri�re-plan',
		'addemoticon' => 'Ins�rer un smiley',
		'selectall' => 'S�lectionner tout',
		'removeformat' => 'Enlever Format'
	),
	'font' => 'Police',
	'fontSize' => 'Taille',
	'bottomButtons' => array(
		'justifyleft' => 'Aligner � gauche',
		'justifycenter' => 'Centrer le texte',
		'justifyright' => 'Aligner � droite',
		'justifyfull' => 'Justifier le texte',
		'insertorderedlist' => 'Ins�rer/Enlever Numbered List',
		'insertunorderedlist' => 'Ins�rer/Enlever Simple List',
		'indent' => 'Augmenter Indentation',
		'outdent' => 'R�duire Indentation',
		'createlink' => 'Ins�rer un Lien',
		'unlink' => 'Se dissocier',
		'insertimage' => 'Ins�rer une Image',
		'inserthorizontalrule' => 'Ins�rer ligne horizontale'
	),
	'editMode' => 'Editer dans le Mode Avanc�',
	'emoticon' => 'Choisir un Smiley',
	'validateMode' => 'Utiliser les outils d\'�diteur avanc�e, enlever la marque de la bo�te \'Advanced Edit\'',
	'createLink' => 'Entrez l\'adresse du lien (Exemple: http://www.domain.com):',
	'insertImage' => 'Entrez le chemin de l\'image:'
);
$P2G_LANGUAGE['COLOR_PICKER_VARS'] = array(
	'popupTitle' => "Cliquer sur la couleur souhait�e",
	'colorTitle' => "Choisir cette couleur"
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtrez la liste ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Ins�rez les Valeurs ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Ajoutez les items choisis',
	'addall' => 'Ajoutez tous les items',
	'rem' => 'Enlevez les items choisis',
	'remall' => 'Enlevez tous les items'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Ajoutez',
	'rem' => 'Enlevez les items choisis',
	'remall' => 'Enlevez tous les items'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Chars";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Disponible:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Ins�r�:";
$P2G_LANGUAGE['FORM_DATA_BIND_MESSAGES'] = array(
	'insertSuccess' => 'Donn�es ins�r�es avec succ�s!',
	'updateEmpty' => 'Il n\'y a pas d\'enregistrement � modifier',
	'updateSuccess' => 'Donn�es mises � jour avec succ�s!',
	'deleteEmpty' => 'Il n\'y a pas d\'enregistrement � supprimer',
	'deleteSuccess' => 'Donn�es supprim�es avec succ�s!',
	'deleteConfirm' => 'Etes-vous certain de supprimer cet enregistrement ?',
	'sortInvalid' => 'Vous devez choisir un champ de tri',
	'gotoEmpty' => 'Vous devez saisir un nombre d\'enregistrement',
	'gotoInvalid' => 'L\'enregistrement demand� est invalide!'
);
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Aller au premier enregistrement pr�c�dent',
	'navPrevTip' => 'Aller � l\'enregistrement',
	'navNextTip' => 'Aller au prochain enregistrement',
	'navLastTip' => 'Aller au dernier enregistrement',
	'actNew' => 'Nouveau',
	'actNewTip' => 'Ajoutez un enregistrement',
	'actEdit' => 'Editer',
	'actEditTip' => 'Editez l\'enregistrement actuel',
	'actDel' => 'Supprimer',
	'actDelTip' => 'Supprimer l\'action actuelle',
	'actSave' => 'Sauvegarder',
	'actSaveTip' => 'Sauvegarder l\'action actuelle',
	'actCancel' => 'Annuler',
	'actCancelTip' => 'Annuler l\'action actuelle',
	'sortAsc' => 'L\'ordre ascendant',
	'sortDesc' => 'L\'ordre descendant',
	'sortChoose' => 'Choisissez un champ de tri',
	'sortTit' => 'Trier',
	'sortFirst' => 'Trier les champs',
	'gotoTip' => 'Saisissez le nombre d\'enregistrements et cliquez sur Allez',
	'goto' => 'Aller',
	'gotoBtnTip' => 'Cliquez � aller � l\'enregistrement choisi',
	'gotoTit' => 'Aller �',
	'filter' => 'Filtrer',
	'filterTip' => 'Choisissez le filtre champ d\'appliquer',
	'filterVTip' => 'Choisissez la valeur du filtre',
	'filterBtnTip' => 'Cliquez ici pour filtrer',
	'filterTit' => 'Filtrer',
	'filterFirst' => 'Filtrer les champs'
);
$P2G_LANGUAGE['REPORT_INVALID_PAGE'] = 'Le nombre de page est invalide!';
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'La requ�te ou les filtres de recherche ont retourn� un set vide',
	'backLink' => 'Retourner au document'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordonner par %s";
$P2G_LANGUAGE['REPORT_ORDER_OPTIONS_LABEL'] = "Ordonner par:";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Premi�re',
	'firstTip' => 'Aller � la premi�re page',
	'prevTit' => 'Pr�c�dente',
	'prevTip' => 'Aller � la page pr�c�dente',
	'prevScrTit' => '%d � l\'envers',
	'prevScrTip' => '%d Pages � l\'envers dans le document',
	'pageTip' => 'Aller � la %de page de %d',
	'nextTit' => 'Suivante',
	'nextTip' => 'Aller � la page suivante',
	'nextScrTit' => '%d en avant',
	'nextScrTip' => '%d Pages en avant dans le document',
	'lastTit' => 'Derni�re',
	'lastTip' => 'Aller � la derni�re page',
	'rowCount' => '%d enregistrement(s)',
	'rowsPerPage' => '%d r�sultat(s) sur chaque page',
	'thisPage' => 'Page %d de %d',
	'rowInterval' => 'Enum�rer %d-%d de %d r�sultats',
	'goTo' => 'Aller � la page'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTitle' => 'Recherche',
	'filtersTitle' => 'Choisir un champ',
	'addBtn' => 'Ajouter',
	'mainOpOr' => 'N\'importe quel filtre',
	'mainOpAnd' => 'Tous les filtres',
	'clearBtn' => 'Supprimer',
	'viewBtn' => 'Vue',
	'sendBtn' => 'Recherche',
	'emptyFilters' => 'Il n\'y a aucun filtre ins�r�',
	'closeFilters' => 'Fermer',
	'removeFilter' => 'Enlever',
	'addedFilter' => 'Filtre Ajout�!',
	'resendConfirmation' => 'Voulez-vous faire la recherche encore avec le(s) filtre(s) actuel(s)?'
);
$P2G_LANGUAGE['REPORT_NUMBER_OPERATORS'] = array(
	'=' => 'Egal �',
	'!=' => 'Pas �gal �',
	'>' => 'Sup�rieur �',
	'<' => 'inf�rieur �',
	'>=' => 'Sup�rieur ou �gal �',
	'<=' => 'Inf�rieur ou �gal �'
);
$P2G_LANGUAGE['REPORT_STRING_OPERATORS'] = array(
	'=' => 'Egal �',
	'!=' => 'Pas �gal �',
	'LIKE' => 'Contenir',
	'NOT LIKE' => 'Ne pas contenir',
	'LIKEI' => 'Commencer avec',
	'LIKEF' => 'Finir avec'
);
$P2G_LANGUAGE['OPERATORS'] = array(
	'EQ' => '�gal �',
	'NEQ' => 'pas �gal �',
	'GT' => 'sup�rieur �',
	'LT' => 'inf�rieur �',
	'GOET' => 'sup�rieur ou �gal �',
	'LOET' => 'inf�rieur ou �gal �',
	'CONTAINING' => 'contenir',
	'STARTING' => 'commencer avec',
	'ENDING' => 'finir avec',
    'BETWEEN' => 'entre',
    'IN' => 'est dans la liste',
    'NOTIN' => 'n\'est pas dans la liste',
    'AND' => 'et',
    'OR' => 'ou'
);
$P2G_LANGUAGE['CHECKBOX_DESCRIPTIONS'] = array(
	'T' => '%s est s�lectionn�',
	'F' => '%s n\'est pas s�lectionn�'
);
$P2G_LANGUAGE['CHECKGROUP_SHORTCUTS'] = array(
	'all' => 'Tout',
	'none' => 'Aucun',
	'invert' => 'Inverser'
);
return $P2G_LANGUAGE;
?>