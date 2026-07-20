<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table = 'mvt_commission';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['id_mvt', 'montant', 'instant'];

    public function getCommissionsParOperateur(): array
    {
        return $this->select(
            'sender_operator.id AS sender_operator_id,
             sender_operator.name AS sender_operator_name,
             receiver_operator.id AS receiver_operator_id,
             receiver_operator.name AS receiver_operator_name,
             CASE
                WHEN sender_operator.id = receiver_operator.id THEN "interne"
                ELSE "inter_operateur"
             END AS type_commission,
             SUM(mvt_commission.montant) AS total_commission,
             COUNT(mvt_commission.id) AS nombre_operations'
        )
            ->join('mvt', 'mvt.id = mvt_commission.id_mvt')
            ->join('prefix AS sender_prefix', 'sender_prefix.value = substr(mvt.num_sender, 1, 3)')
            ->join('operator AS sender_operator', 'sender_operator.id = sender_prefix.operator_id')
            ->join('prefix AS receiver_prefix', 'receiver_prefix.value = substr(mvt.num_receiver, 1, 3)')
            ->join('operator AS receiver_operator', 'receiver_operator.id = receiver_prefix.operator_id')
            ->groupBy('sender_operator.id, receiver_operator.id')
            ->orderBy('sender_operator.name', 'ASC')
            ->orderBy('receiver_operator.name', 'ASC')
            ->findAll();
    }

    public function getMontantsAEnvoyerParOperateur(): array
    {
        return $this->select(
            'receiver_operator.id AS operator_id,
             receiver_operator.name AS operator_name,
             SUM(mvt.montant) AS montant_total,
             SUM(mvt_commission.montant) AS commission_total,
             COUNT(mvt_commission.id) AS nombre_transferts,
             CASE
                WHEN SUM(mvt.montant) > 0 THEN "a_envoyer"
                ELSE "rien_a_envoyer"
             END AS statut_envoi'
        )
            ->join('mvt', 'mvt.id = mvt_commission.id_mvt')
            ->join('prefix AS receiver_prefix', 'receiver_prefix.value = substr(mvt.num_receiver, 1, 3)')
            ->join('operator AS receiver_operator', 'receiver_operator.id = receiver_prefix.operator_id')
            ->groupBy('receiver_operator.id')
            ->orderBy('receiver_operator.name', 'ASC')
            ->findAll();
    }

    public function getConfigurationCommissions(): array
    {
        return $this->db->table('comission')
            ->select(
                'op1.id AS operator_sender_id,
                 op1.name AS operator_sender_name,
                 op2.id AS operator_receiver_id,
                 op2.name AS operator_receiver_name,
                 CASE
                    WHEN op1.id = op2.id THEN 0
                    ELSE comission.commission_rate
                 END AS commission_rate,
                 CASE
                    WHEN op1.id = op2.id THEN "meme_operateur"
                    ELSE "inter_operateur"
                 END AS type_commission'
            )
            ->join('operator AS op1', 'op1.id = comission.id_op1')
            ->join('operator AS op2', 'op2.id = comission.id_op2')
            ->orderBy('op1.name', 'ASC')
            ->orderBy('op2.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function findRate(int $senderOperatorId, int $receiverOperatorId): ?float
    {
        $commission = $this->db->table('comission')
            ->where('id_op1', $senderOperatorId)
            ->where('id_op2', $receiverOperatorId)
            ->get()
            ->getRowArray();

        return $commission === null ? null : (float) $commission['commission_rate'];
    }

    public function enregistrerCommission(int $mvtId, float $montant, string $instant): int
    {
        return (int) $this->insert([
            'id_mvt' => $mvtId,
            'montant' => $montant,
            'instant' => $instant,
        ], true);
    }
}
