import { Plan } from '../services/api';

interface PlanCardProps {
  plan: Plan;
  isActive?: boolean;
  onSelect: (planId: number) => void;
  disabled?: boolean;
}

export function PlanCard({ plan, isActive, onSelect, disabled }: PlanCardProps) {
  return (
    <div className="bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
      {/* Header laranja */}
      <div className="pl-0 pr-4 pt-6 pb-4">
        <div className="bg-orange-500 text-white p-4 min-h-[80px] flex items-center mr-6">
          <h3 className="font-semibold text-base leading-tight">
            {plan.description.replace(' / ', '\n/').split('\n').map((line, index) => (
              <div key={index}>{line}</div>
            ))}
          </h3>
        </div>
      </div>
      
      {/* Conteúdo do card */}
      <div className="p-6 flex-1 flex flex-col">
        {/* Preço */}
        <div className="mb-6">
          <p className="text-gray-600 text-sm mb-1">Preço:</p>
          <div className="flex items-baseline">
            <span className="text-3xl font-bold text-gray-800">
              R$ {parseFloat(plan.price).toFixed(2).replace('.', ',')}
            </span>
            <span className="text-gray-500 text-sm ml-1">/mês</span>
          </div>
        </div>
        
        {/* Armazenamento */}
        <div className="mb-6 flex-1">
          <p className="text-gray-600 text-sm mb-1">Armazenamento:</p>
          <p className="text-2xl font-bold text-gray-800">
            {plan.gigabytesStorage} GB
          </p>
        </div>
        
        {/* Botão */}
        <div className="mt-auto">
          {isActive ? (
            <div className="bg-blue-500 text-white text-center py-3 px-4 rounded font-medium">
              Plano Atual
            </div>
          ) : (
            <button
              onClick={() => onSelect(plan.id)}
              disabled={disabled}
              className={`w-full py-3 px-4 rounded font-medium transition-colors ${
                disabled
                  ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  : 'bg-orange-500 hover:bg-orange-600 text-white'
              }`}
            >
              {disabled ? 'Processando...' : 'Selecionar Plano'}
            </button>
          )}
        </div>
      </div>
    </div>
  );
}