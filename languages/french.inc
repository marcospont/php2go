<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go ne marche pas avec la configuration actuelle de la serveur.<BR>Votre version PHP est <I>%s</I>. Les besoins minimals du framework est <I>%s</I>.<BR>Installer une version égale ou supérieure de marcher normalement.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "L'extension <I>%s</I> n'est pas supporté par l'installation PHP actuelle.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Erreur dans la classe %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Erreur dans le script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Error dans la base de données";
$P2G_LANGUAGE['ERR_INVALID_VALUE'] = "Valeur invalide!";
$P2G_LANGUAGE['ERR_DUPLICATED_VALUE'] = "Cette valeur était déjà inséré!";
$P2G_LANGUAGE['ERR_COMM_FAILURE'] = "Echec de communication";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Ce n'était pas possible de charger les paramètres de la connexion de base de données! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "La connexion des paramètres de base de données sont invalide! Les entrées USER, BASE et TYPE sont obligatoire. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Ce n'était pas possible à connecter à la base de données avec la configuration d'accès actuelle. Sont-ils correctes?";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS_PATH'] = "Ce n'était pas possible d'importer et/ou créer une instance de la classe de connexion %s!";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS'] = "La classe de connexion %s n'est pas une sousclasse de php2go.db.Db!";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Erreur fatale",
	2 => "Alerte",
	4 => "Erreur syntaxe",
	8 => "Avis",
	16 => "Erreur fatale du noyau",
	32 => "Alerte du noyau",
	64 => "Compiler Fatal Error",
	128 => "Alerte de compilateur",
	256 => "Erreur d'usager",
	512 => "Alerte d'usager",
	1024 => "Avis d'usager"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "L'entrée 'ABSOLUTE_URI' n'était pas fixer dans la tableau globale de configurations!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "L'entrée <I>%s</I> n'était pas fixé dans le tableau globale de configurations!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "La fichier de messages pour la domaine %s, langage %s, est invalide ou n'exist pas.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Ce n'était pas possible de trouver l'entrée <I>%s</I> dans la tableau globale de configurations. Etait-il fixé correctement dans l'installation?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Ce n'était pas possible de charger le module <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Ce n'était pas possible de charger les modules d'un dossier valide à <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Ce n'était pas possible de charger la fichier <I>%s</I> à <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Ce n'était pas possible de convertir le date à la forme %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "La classe %s ne marche pas sur Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "La fonction %s n'existe pas!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "La méthode %s n'est pas défini dans la classe %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "La classe <I>%s</I> est abstraite et vous ne pouvez pas crée une objet de cette classe!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "La date %s n'est pas sous la forme requis %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "La variable %s envoyé à la fonction ou méthode %s n'est pas une ressource valide.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "L'authenticator %s n'est pas une sousclasse de php2go.auth.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Ce n'était pas possible d'importer et/ou créer un instance du authenticator %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "Le conteneur d'utilisateur %s n'est pas une sousclass de  php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH' ] = "Ce n'était pas possible d'importer et/ou créer un instance du conteneur d'utilisateur %s!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER'] = "L'autoriseur %s n'est pas une sousclasse de php2go.auth.Authorizer!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER_PATH'] = "Ce n'était pas possible d'importer et/ou créer un instance de l'autoriseur %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Une erreur s'est passé en analysant la fichier XML:<BR>Erreur: %s<BR>Ligne: %s, Colonne: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Un erreur s'est passé en analysant le code PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Ce n'était pas possible de trouver la fichier ou dossier %s. Existe-t-il?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Ce n'était pas possible d'ouvrir la fichier %s avec la mode %s. Vérifier les permissions entrées/sorties et the used mode.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Ce n'était pas possible de créer la fichier ou dossier %s. Vérifier les permissions entrées/sorties de la dossier parent.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Ce n'était pas possible d'ouvrir la fichier %s pour lire. Existe-t-il? Est-ce que le chemin correcte?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Ce n'était pas possible d'ouvrir la fichier ou dossier %s pour écrire. Avez-vous la permission de l'écraser?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Ce n'était pas possible de detruire la fichier %s.<BR>Avez-vous la permission d'écrire à cette dossier? Est-ce que la fichier a la permission d'être détruire?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Ce n'était pas possible d'ouvrir la fichier %s pour inclure. Existe-t-il? Est-ce que le chemin correcte?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Ce n'était pas possible d'appliquer la mode %s à la fichier ou dossier %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Ce n'était pas possible de trouver la dossier %s. Existe-t-il?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Ce n'était pas possible d'ouvrir la dossier %s pour lire. Vérifier ses permissions.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Ce n'était pas possible d'ouvrir la fichier ce qui contenant l'objet sérialisé pour lire. Est-ce que le chemin correcte? Etait la fichier crée?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "La requête inséré dans la table '%s' retourné un set vide d'utiliser les valeurs fournies.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "La requête actualisé dans la table '%s' avec la clause %s retourné un set vide d'utiliser les valeurs fournies.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "La requête %s, utilisé de publier les variables dans une in the global scope, retourné un set vide.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "La requête passé à la méthode Db::toGlobals() doit commencer avec 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Ce n'était pas possible d'initialiser le gabarit avec un nom de fichier vide!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "Le gabarit n'était pas préparé, parce que son contenu est vide. Vérifier le contenu passé au constructeur.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Ce n'était pas possible de trouver la variable %s dans le gabarit <I>%s</I>. <BR>Recevoir des résultats correctes de cet outil, insérez {%s} dans la code du gabarit.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Ce n'était pas possible de trouver le bloc <I>%s</I> pour création. Est-il declaré dans le gabarit?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Ce n'était pas possible de trouver le bloc %s de réutiliser comme %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "L'elément %s était déjà configuré with the méthode DocumentElement::parse(). Ce n'était pas possible d'ajouter des eléments avec la méthode put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "Le gabarit %s n'était pas préparé. Gèrez la méthode %s::parse(); avant exécutez les opérations with the object.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Ce gabarit avait déjà préparé. Vous devez appeler le méthode parse() seulement une fois.";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_BLOCKDEF'] = "La définition du bloc <I>%s</I> est déséquilibrée!";
$P2G_LANGUAGE['ERR_TPLPARSE_INCOMPLETE_BLOCKDEF'] = "La spécification du bloc <I>%s</I> est incomplet à l'intérieur du tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_BLOCKINSIDELOOP'] = "Ce n'est pas possible de définir des blocs dynamiques à l'intérieur de boucles!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_TAG'] = "La définition du tag <I>%s</I> est déséquilibrée!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNEXPECTED_TAG'] = "Tag inattendu <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_PARENTHESIS'] = "Parenthèse déséquilibrée  à l'intérieur de la formule <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_TAG_SYNTAX'] = "La définition du tag <I>%s</I> est invalide : %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TOKEN'] = "Token invalide à l'intérieur de la formule <I>%s</I> : %s";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTE'] = "Manqué or invalide valeur de nom de l'attribut!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTENAME'] = "<I>%s</I> n'est pas un valide nom de l'attribut!";
$P2G_LANGUAGE['ERR_TPLPARSE_REQUIRED_ATTRIBUTE'] = "La propriété <I>%s</I> est requise dans la définition du tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ATTRIBUTE'] = "La propriété <I>%s</I> est invalide dans la définition du tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_ASSIGN'] = "La caractère '=' n'était pas trouvé après le nom de l'attribut!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_IS_EXPR'] = "L'opération est invalide pour une opération 'is' : <I>%s</I>";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_TAG_ARGS'] = "Le tag <I>%s</I> doit avoir des arguments!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ARGS'] = "Le tag <I>%s</I> ne peut pas contenir des arguments!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_MODIFIER'] = "Le modificateur <I>%s</I> est invalide ou n'existe pas!";
$P2G_LANGUAGE['ERR_WIDGET_INCLUDE'] = "C'est possible d'utiliser le widget <i>%s</i> seulement entre les marquers INCLUDE WIDGET!";
$P2G_LANGUAGE['ERR_WIDGET_MANDATORY_PROPERTY'] = "La propriété <i>%s</i> est requise dans la définition du widget <i>%s</i>!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "Il n'y a pas de eléments défini dans la plan de document fourni.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "Le bloc de base d'un gabarit ne peut pas être écrasé.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "La valeur de %s doit être supérieur à zéro dans la fonction %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "La valeur de %s doit être un tableau en la fonction %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "La valeur de '%s' is out of the allowed bounds: %s and %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "La valeur de <I>%s</I> n'est pas une URL valide!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> n'est pas une valide hôte de connexion!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Ce n'était pas possible de trouver une DNS valide à la domaine %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "La propriété %s de la variable de la session %s n'était pas trouvé. Exécutez la fonction createProperty() ";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "Le minuteur %s de la variable de la session %s n'était pas trouvé. Exécutez la fonction createTimeCounter() ";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "L'object passé comme paramètre n'est pas un instance valide de la classe <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Paramètres insuffisantes de construire la calcultrice!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Paramètres insuffisantes de construire la sélection de la couleur!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "La mèthode %s n'est pas valide de sousmettre la forme '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> n'est pas un nom valide pour un fonction de validation !";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "Le champ <I>%s</I> est déjà défini dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "Le bouton <I>%s</I> déja existe dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "The section ID <I>%s</I> était déjà défini dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Ce n'était possible de la visibilité de la section <I>%s</I>, parce que la fonction <I>%s</I> n'est pas défini!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "La section <I>%s</I> est conditionel. Vouz devez déclarer un bloc nommé <I>%s</I> dans le gabarit contenu the section champs!";
$P2G_LANGUAGE['ERR_FORM_REQUIRED_SUMMARY'] = "Les champs suivantes dans la forme sont requis:";
$P2G_LANGUAGE['ERR_FORM_ERRORS_SUMMARY'] = "Cette forme contient les erreurs suivantes:";
$P2G_LANGUAGE['ERR_FORM_COMPLETE_FIELDS'] = "S'il vous plaît, complétez les champs et essayez encore.";
$P2G_LANGUAGE['ERR_FORM_FIX_FIELDS'] = "S'il vous plaît, corrigez les champs et essayez encore.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "Le champ %s est obligatoire.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID'] = "Le champ %s a une valeur incorrecte!";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_DATATYPE'] = "Le champ %s contient une valuer invalide %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "La précision numerique du champ %s est invalide.\nLes tailles maximum sont %s pour la partie entier et %s pour la partie décimale.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "Le champ %s doit contenir seulement les caractères alphanumeriques.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "Le champ %s doit contenir une des valeurs suivantes: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "Le champ %s contient un nombre de carte de crédit invalide %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "Le champ %s doit avoir au moins %s caractères.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "Le champ %s must have until %s caractères.";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "Le champ %s doit être égal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "Le champ %s ne peut pas égal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "Le champ %s doit être supérieur au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "Le champ %s doit être inférieur que champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "Le champ %s doit être supérieur ou égal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "Le champ %s doit être inférieur ou égal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "Le champ %s doit être égal à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "Le champ %s can't be ou égal à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "Le champ %s doit être supérieur à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "Le champ %s doit être inférieur à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "Le champ %s doit être supérieur ou égal à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "Le champ %s doit être inférieur ou égal à %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "The mask <I>%s</I>, sur le champ <I>%s</I> n'est pas valide!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "Le champ type <I>%s</I> n'est pas valide!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "Le code éntré dans le champ %s ne coordone pas le contenu de l'image!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "Le champ type <I>%s</I> ne peut pas utiliser dans un <I>grid</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "La valeur du attribut <I>CELLSIZES</I> dans le champ <I>%s</I> est invalide. Vous devez fournir N+1 tailles (N=FIELDSET size) et la somme des tailles doit être égal à 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "The query defined in the <I>DATASOURCE</I> du champ <I>%s</I> doit retourné N+2 colonnes (N=FIELDSET size)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Les attributs de la contrôleur d'événements <I>%s</I> sont invalides ou incomplets!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Les attributs de la régle <I>%s</I> sont invalides ou incomplets!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "La spécification de la marqueur DATASOURCE dans le champ <I>%s</I> est invalide.<BR>Les noeuds enfants KEYFIELD et LOOKUPTABLE sont obligatoiresy!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "La spécification du champ <I>%s</I> est incomplet. Vous devez fournir au moins noeud enfant &lt;OPTION&gt; !";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Le attribut VALUE de l'option au indice <I>%d</I> dans le champ <I>%s</I> est manquant!";
$P2G_LANGUAGE['ERR_MISSING_DBGROUPFIELD_DATASOURCE'] = "Il n'était pas défini un marquer DATASOURCE tag au %s <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBGROUPFIELD_RESULTS'] = "La requête fournie dans l'élément DATASOURCE element of the %s <I>%s</I> returned an empty set!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "L'attribut VALUE de l'option à indice %d dans le COMBOFIELD <I>%s</I> est manquant!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "The spécification du EDITSELECTIONFIELD <I>%s</I> est incomplet. Les noeuds enfants EDITFIELD et LOOKUPFIELD sont obligatoires!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "The spécification du LOOKUPSELECTIONFIELD <I>%s</I> a tort. Le noeud doit avoir deux enfants LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "Il n'y a aucun DATASOURCE défini dans le premier enfant LOOKUPFIELD du LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Les items \$fields et \$tables sont obligatoires dans la construction de la rêquete SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "La structure de la grille <I>%s</I> est invalide. Vouz devez déclarer un <I>DATASOURCE</I> et un <I>FIELDSET</I> contenir au moins un champ!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "La spécification du RANGEFIELD <I>%s</I> a tort. Le noeud doit avoir deux enfants EDITFIELD, COMBOFIELD, LOOKUPFIELD ou DATEPICKERFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "La spécification du DATAFILTER <I>%s</I> est invalide.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "Le masque assigné au DATAFILTER <I>%s</I> est invalide.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Déjà existe un autre DATAFILTER avec ID <I>%s</I>.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "La spécification du  EDITSEARCHFIELD <I>%s</I> est incomplet. Vous devez fournir au moins un DATAFILTER, un DATASOURCE et un LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Ce n'était pas possible de compléter l'opération!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "La recherche ne pouvait pas effectué parce que tous les champs de recherche sont vides.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "La recherche ne pouvait pas effectué car les valeurs de filtres sont vides ou plus courtes que %d caratères.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "The grouping columns count doit être inférieur au compte de colonnes!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "La colonne <I>%s</I> n'est pas défini dans la résultat de la requête!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "La variable <I>%s</I> de la bloc <I>%s</I> n'était pas declaré dans le gabarit!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "La valeur <I>%s</I> n'est pas une définition valide aux tailles de colonnes!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "La somme des tailles de colonnes passé à la fonction setColumnSizes doit être égal à 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "Le compte de tailles colonnes fourni (%s) n'est pas égal au compte de colonnes of the report (%s, %s grouping)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Ce n'était pas possible de consruire la forme de recherche, parce que la variable <I>%s</I> n'était pas trouvé. <BR>Déclarez {%s} dans le gabarit du document à continuer.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Vous devez fournir le Label, le Field Name et la Mask dans la construction des paramètres de recherche!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "La masque <I>%s</I> du paramètre de recherche  <I>#%d</I> est invalide!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "La fonction setAlternateStyle doit recevoir au moins deux paramètres (CSS names)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Une colonne cachée (%s) ne peut pas être inclut des titres de groupe!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Une colonne displayed in the group headers (%s) ne peut pas être défini comme cachée!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "La nombre des colonnes cachées doit être smaller than the requête  columns count!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "La colonne <I>%s</I> qui est défini comme cachée n'existe pas dans XXXX the requête  result set!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "Le type <I>%s</I> n'est pas un type valide de DataSet!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "The requête gardé dans le paramètre \$rootSql de la méthode Menu::loadFromDatabase() retourné un set vide ou elle n'a pas les champs requis et minumums (CAPTION and LINK)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "The requête passé dans le paramètre \$childSql de la méthode Menu::loadFromDatabase() est invalide.<BR>Fournissez une expression qui contenu le '~var~' (Relation Column) plus les deux champs requis (CAPTION and LINK)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "Le menu nétait pas crée. Construisez les items du menu utilisant Menu::loadFromDatabase() ou Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "Le noeud racine de la fichier XML n'a aucun enfants!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "L'image <I>%s</I> n'est pas valide. Les valeurs possibles sont: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Ce n'était pas possible d'ouvrir la connexion. Vous devez établir la connexion, utilisant les méthode FtpClient::setServer() et FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Ce n'était pas possible d'authentifier. Une des valuers d'usager (nom d'usager ou mot de passe) n'était pas fournie.";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Le limites de spreadsheet était surpassé: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "La taille maximum pour un commentaire de la cellule était surpassé<BR>Valeur: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "La maximum de quatre polices pour chaque spreadsheet était surpassé!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Ce n'était pas possible de compléter the upload. The file wasn't transferred correctly.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Ce n'était pas possible de compléter the upload. La taille de la fichier est supérieur à la taille maximum permis.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "Ce n'était pas possible de compléter la transmission de la fichier. Le nom de fichier %s est invalide.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "Ce n'était pas possible de compléter la transmission de la fichier. L'extension %s n'est pas permis.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Ce n'était pas possible de compléter la transmission de la fichier. La fichier <I>%s</I> déjà existe et n'était pas écrasé.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Ce n'était pas possible de déplacer la fichier temporaire à la dossier destinaire.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Ce n'était pas possible d'ouvrir le socket utilisant le port %s avec la hôte %s!%s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "La connection avec le socket n'est pas active!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Fin de fichier attendu, l'opération ne peut pas être accompli!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "La temporisation socket était surpassé. L'opération ne peut pas être accompli!";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Il n'y a aucun destinataires pour le message!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Ce n'était pas possible de signer le contenu de le message.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Erreur en connectant à la serveur SMTP %s!<BR>[Erreur %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Erreur en exécutant la commande %s sur la serveur SMTP!<BR>[Erreur %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Ce n'était pas possible d'authentifier avec la serveur SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Crypter ou décrypter données, vous devez fournir un clé avec méthode Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Ce n'était pas possible to initialiser le cryptogramme <I>%s</I> utilisant la mode %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Ce n'était pas possible d'extraire une clé publique de crypter les données. Fournissez un fichier contenu un certificat valide à la méthode OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Ce n'était pas possible d'extraire une clé publique de vérifier la signature inclu dans cette données. Fournissez un fichier contenu un certificat valide à la méthode OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Ce n'était pas possible d'extraire une clé privé de décrypter les données. Fournissez un fichier qui contient la clé privé et la phrase de passe, si la clé l'a besoin.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Ce n'était pas possible d'extraire une clé privé to generate the digital signature. Fournissez un fichier qui contient une clé privé et la phrase de passe, si la clé crée l'a besoin.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "Le chemin d'accès du certificat est invalide!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Ce n'était pas possible de lire le contenu du certificat %s!";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Erreur en connectant à la serveur POP3 %s!<BR>[Erreur %d] - %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Erreur en exécutant la commande %s sur la serveur POP!<BR>%s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Ce n'était pas possible d'authentifier avec la serveur POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Ce n'était pas possible gérer la commande APOP : il n'y a pas de bannière d'authentification !";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Ce n'était pas possible d'établir un connexion HTTP avec la hôte <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "La hôte HTTP n'était pas fixé. Gérez la méthode HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Ce n'était pas possible créer une instance de la classe de validation <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "La classe de validation <I>%s</I> est invalide! La méthode %s::execute() ne peut pas être trouvé.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "L'expression évalué n'a pas donné le résultat prévu.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "La fonction ou mèthode <I>%s</I> n'est pas une callback valide!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "La fichier <I>%s</I> ne contient aucune données de configuration valide!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> n'est past une spécification valide de coleur.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Ce n'était pas possible d'attribuer une nouvelle coleur pour cette image!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Il y a un nom de handler JSRS ou alias manquant!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "Le handler JSRS %s est invalide!";
$P2G_LANGUAGE['ERR_AJAX_SUPPORT'] = "Votre navigateur web ne peut pas fonctionner AJAX!";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPNAME'] = "JSON Decode Error: %s n'est pas un valide nom de l'attribut";
$P2G_LANGUAGE['ERR_JSON_MISSING_COLON'] = "JSON Decode Error: deux points manquants à position %d";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPVALUE'] = "JSON Decode Error: %s n'est pas une valide valeur de l'attribut";
$P2G_LANGUAGE['ERR_JSON_MISSING_COMMA'] = "JSON Decode Error: virgule manquant à position %d";
$P2G_LANGUAGE['ERR_JSON_ESCAPE_SEQUENCE'] = "JSON Decode Error: invalide séquence trouvé à position %d";
$P2G_LANGUAGE['ERR_JSON_UNTERMINATED_STRING'] = "JSON Decode Error: string inachevé trouvé à %d";
$P2G_LANGUAGE['ERR_JSON_CYCLE'] = "L'encodeur JSON a trouvé un cycle dans un instance de la classe %s!";
$P2G_LANGUAGE['ERR_JSON_ENCODE'] = "Une erreur était trouvé en convertissant le type %s à  un string JSON!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Soumettre";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Ouvrir la calendrier";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Ouvrir la calculatrice";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "La Calculatrice";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "Résultat";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Uses %s Compression -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Original Size: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Packed Size: %d";
$P2G_LANGUAGE['EMPTY_SEARCH'] = 'Vous devez choisir un filtre et entrez une terme de recherche!!';
$P2G_LANGUAGE['EMPTY_RESULTS'] = 'La recherche a retourné un résultat vide!';
$P2G_LANGUAGE['FORM_INPUT_MASK_MESSAGES'] = array(
	'optionalCharFirst' => "L'indicateur faculatif doit suivre un literal.",
	'escapeCharLast' => "L'indicateur échappatoire doit précéder un literal.",
	'nestedCharClass' => "La classe de la caractère est invalide.",
	'invalidLimits' => "La définition de la grandeur est invalide."
);
$P2G_LANGUAGE['FORM_MASKS'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'Devise',
	'DATE' => 'Date',
	'TIME' => 'L\'heure',
	'EMAIL' => 'Couriel',
	'FLOAT' => 'Nombre Flottant',
	'INTEGER' => 'Nombre Entier',
	'DIGIT' => 'Nombre',
	'IP' => 'Addresse IP',
	'WORD' => 'Texte alphanumérique',
	'URL' => 'URL/Addresse internet',
	'ZIP' => 'Code Postale'
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
		'pre' => 'Formatted &lt;PRE&gt;',
		'removeformat' => 'Enlever Format'
	),
	'topButtons' => array(
		'bold' => 'Format en gras',
		'italic' => 'Format Italique',
		'underline' => 'Format Souligne',
		'strikethrough' => 'Formatez comme texte barré',
		'cut' => 'Couper',
		'copy' => 'Copier',
		'paste' => 'Paste',
		'pickforecolor' => 'Changer la couleur avant-plan',
		'pickbackcolor' => 'Changer la couleur arrière-plan',
		'addemoticon' => 'Insérer un smiley',
		'selectall' => 'Sélectionner tout',
		'removeformat' => 'Enlever Format'
	),
	'font' => 'Police',
	'fontSize' => 'Taille',
	'bottomButtons' => array(
		'justifyleft' => 'Aligner à gauche',
		'justifycenter' => 'Centrer le texte',
		'justifyright' => 'Aligner à droite',
		'justifyfull' => 'Justifiez le texte',
		'insertorderedlist' => 'Insérer/Enlever Numbered List',
		'insertunorderedlist' => 'Insérer/Enlever Simple List',
		'indent' => 'Augmenter Indentation',
		'outdent' => 'Réduire Indentation',
		'createlink' => 'Insérer une Lien',
		'unlink' => 'Se dissocier',
		'insertimage' => 'Insérer une Image',
		'inserthorizontalrule' => 'Insérer ligne horizontale'
	),
	'editMode' => 'Editer dans la Mode Avancée',
	'emoticon' => 'Choisir un Smiley',
	'validateMode' => 'Utiliser les outils d\'éditeur avancée, enlevez la marque de la boîte \'Advanced Edit\'',
	'createLink' => 'Entrez l\'addresse de la lien (Exemple: http://www.domain.com):',
	'insertImage' => 'Entrez le chemin de l\'image:'
);
$P2G_LANGUAGE['COLOR_PICKER_VARS'] = array(
	'popupTitle' => "Cliquer la couleur vous voulez",
	'colorTitle' => "Choisir cette couleur"
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtrez la liste ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Insérez les Valeurs ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Ajoutez les items choisis',
	'addall' => 'Ajoutez tous les items',
	'rem' => 'Enlevez les items choisis',
	'remall' => 'Enlevez tous'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Ajoutez',
	'rem' => 'Enlevez les items choisis',
	'remall' => 'Enlevez tous'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Chars";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Disponible:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Inséré:";
$P2G_LANGUAGE['FORM_DATA_BIND_MESSAGES'] = array(
	'insertSuccess' => 'Données inséré avec succès!',
	'updateEmpty' => 'Il n\'y a pas de records d\'editer!',
	'updateSuccess' => 'Données actualisé avec succès!',
	'deleteEmpty' => 'Il n\'y a pas de records de détruire!',
	'deleteSuccess' => 'Données détruit avec succès!',
	'deleteConfirm' => 'Etes-vous certain vous voulez détruire ce record?',
	'sortInvalid' => 'Vous devez choisir un champ d\'ordonner!',
	'gotoEmpty' => 'Vous devez entrer un nombre de record à aller!',
	'gotoInvalid' => 'Le record demandée est invalide!'
);
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Aller au premier record précédente',
	'navPrevTip' => 'Aller au record',
	'navNextTip' => 'Aller au prochain record',
	'navLastTip' => 'Aller au dernier record',
	'actNew' => 'New',
	'actNewTip' => 'Créez une record nouvelle',
	'actEdit' => 'Editer',
	'actEditTip' => 'Editez la record actuelle',
	'actDel' => 'Détruire',
	'actDelTip' => 'Détruire l\'action actuelle',
	'actSave' => 'Sauvegarder',
	'actSaveTip' => 'Sauvegarder l\'action actuelle',
	'actCancel' => 'Annuler',
	'actCancelTip' => 'Annuler l\'action actuelle',
	'sortAsc' => 'L\'ordre ascendant',
	'sortDesc' => 'L\'ordre descendant',
	'sortChoose' => 'Choisissez un champ d\'ordonner',
	'sortTit' => 'Ordonner',
	'sortFirst' => 'Ordonner les champs',
	'gotoTip' => 'Donnez la nombre de la record et cliquez Allez',
	'goto' => 'Aller',
	'gotoBtnTip' => 'Cliquez à aller à la record choisi',
	'gotoTit' => 'Aller à',
	'filter' => 'Filtrer',
	'filterTip' => 'Choisissez the filter champ d\'appliquer',
	'filterVTip' => 'Choisissez la valeur du filtre',
	'filterBtnTip' => 'Cliquez ici de filtrer',
	'filterTit' => 'Filtrer',
	'filterFirst' => 'Filtrer les champs'
);
$P2G_LANGUAGE['REPORT_INVALID_PAGE'] = 'Le nombre de page est invalide!';
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'The requête  ou les filtres de recherche a rétourné un set vide',
	'backLink' => 'Retourner au document'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordonner par %s";
$P2G_LANGUAGE['REPORT_ORDER_OPTIONS_LABEL'] = "Ordonner par:";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Première',
	'firstTip' => 'Aller à la première page',
	'prevTit' => 'Précédente',
	'prevTip' => 'Aller à la page précédente',
	'prevScrTit' => '%d à l\'envers',
	'prevScrTip' => '%d Pages à l\'envers dans le document',
	'pageTip' => 'Aller à la %de page de %d',
	'nextTit' => 'Suivante',
	'nextTip' => 'Aller à la page suivante',
	'nextScrTit' => '%d en avant',
	'nextScrTip' => '%d Pages en avant dans le document',
	'lastTit' => 'Dernière',
	'lastTip' => 'Aller à la dernière page',
	'rowCount' => '%d record(s) trouvé(s)',
	'rowsPerPage' => '%d résultat(s) sur chaque page',
	'thisPage' => 'Page %d de %d',
	'rowInterval' => 'Enumerer %d-%d de %d résultats',
	'goTo' => 'Aller à la page'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTitle' => 'Recherche',
	'filtersTitle' => 'Choisir un champ',
	'addBtn' => 'Ajouter',
	'mainOpOr' => 'N\'importe quel filter',
	'mainOpAnd' => 'Tous les filtres',
	'clearBtn' => 'Supprimer',
	'viewBtn' => 'Vue',
	'sendBtn' => 'Recherche',
	'emptyFilters' => 'Il n\'y a aucun filtres inséré',
	'closeFilters' => 'Fermer',
	'removeFilter' => 'Enlever',
	'addedFilter' => 'Filtre Ajouté!',
	'resendConfirmation' => 'Voulez-vous faire la recherche encore avec le(s) filtre(s) actuel(s)?'
);
$P2G_LANGUAGE['REPORT_NUMBER_OPERATORS'] = array(
	'=' => 'Egal à',
	'!=' => 'Pas égal à',
	'>' => 'Supérieur à',
	'<' => 'inférieur à',
	'>=' => 'Supérieur or égal à',
	'<=' => 'Inférieur or égal à'
);
$P2G_LANGUAGE['REPORT_STRING_OPERATORS'] = array(
	'=' => 'Egal à',
	'!=' => 'Pas égal à',
	'LIKE' => 'Contenir',
	'NOT LIKE' => 'Ne pas contenir',
	'LIKEI' => 'Commencer avec',
	'LIKEF' => 'Finir avec'
);
$P2G_LANGUAGE['OPERATORS'] = array(
	'EQ' => 'egal à',
	'NEQ' => 'pas égal à',
	'GT' => 'supérieur à',
	'LT' => 'inférieur à',
	'GOET' => 'supérieur or égal à',
	'LOET' => 'inférieur or égal à',
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
	'T' => '%s est sélectionné',
	'F' => '%s n\'est pas sélectionné'
);
$P2G_LANGUAGE['CHECKGROUP_SHORTCUTS'] = array(
	'all' => 'Tout',
	'none' => 'Aucun',
	'invert' => 'Inverser'
);
return $P2G_LANGUAGE;
?>