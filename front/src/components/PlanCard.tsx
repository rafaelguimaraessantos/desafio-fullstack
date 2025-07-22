import { Plan } from '../services/api';

interface PlanCardProps {
  plan: Plan;
  isActive?: boolean;
  onSelect: (planId: number) => void;
  disabled?: boolean;
}

export function PlanCard({ plan, isActive, onSelect, disabled }: PlanCardProps) {
  return (
    <div className={`border rounded-lg p-6 ${isActive ? 'border-blue-500 bg-blue-50' : 'border-gray-200'}`}>
      <h3 className="text-lg font-semibold mb-2">{plan.description}</h3>
      <div className="space-y-2 mb-4">
        <p className="text-sm text-gray-600">
          <span className="font-medium">Clientes:</span> {plan.numberOfClients}
        </p>
        <p className="text-sm text-gray-600">
          <span className="font-medium">Armazenamento:</span> {plan.gigabytesStorage}GB
        </p>
        <p className="text-2xl font-bold text-green-600">
          R$ {parseFloat(plan.price).toFixed(2).replace('.', ',')}
          <span className="text-sm font-normal text-gray-500">/mês</span>
        </p>
      </div>
      
      {isActive ? (
        <div className="bg-blue-500 text-white text-center py-2 px-4 rounded font-medium">
          Plano Atual
        </div>
      ) : (
        <button
          onClick={() => onSelect(plan.id)}
          disabled={disabled}
          className={`w-full py-2 px-4 rounded font-medium transition-colors ${
            disabled
              ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
              : 'bg-orange-500 hover:bg-orange-600 text-white'
          }`}
        >
          {disabled ? 'Processando...' : 'Selecionar Plano'}
        </button>
      )}
    </div>
  );
}