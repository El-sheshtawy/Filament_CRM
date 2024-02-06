<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Dashboard;
use Filament\Pages\SimplePage;
use Phpsa\FilamentPasswordReveal\Password;

class AcceptInvitation extends SimplePage
{
    use InteractsWithForms, InteractsWithFormActions;

    protected static string $view = 'livewire.accept-invitation';

    public int $invitation;
    private Invitation $invitationModel;

    public ?array $data = [];

    public function mount(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $this->form->fill([
            'email' => $this->invitationModel->email
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-panels::pages/auth/register.form.name.label'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label(__('filament-panels::pages/auth/register.form.email.label'))
                    ->disabled(),
                Password::make('password')
                    ->label(__('filament-panels::pages/auth/register.form.password.label'))
                    ->revealable()
                    ->hideIcon('heroicon-s-eye-slash')
                    ->showIcon('heroicon-o-eye')
                    ->copyable(true)
                    ->copyIcon('heroicon-o-clipboard')
                    ->required()
                    ->same('passwordConfirmation')
                    ->rule(\Illuminate\Validation\Rules\Password::default())
                    ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),
                Password::make('passwordConfirmation')
                    ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                    ->revealable()
                    ->hideIcon('heroicon-s-eye-slash')
                    ->showIcon('heroicon-o-eye')
                    ->copyable(true)
                    ->copyIcon('heroicon-o-clipboard')
                    ->required()
                    ->rule(\Illuminate\Validation\Rules\Password::default())
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $this->invitationModel = Invitation::find($this->invitation);

        $user = User::create([
            'name' => $this->form->getState()['name'],
            'password' => $this->form->getState()['password'],
            'email' => $this->invitationModel->email,
            'role_id' => Role::query()->where('name', 'Employee')->value('id')
        ]);

        auth()->login($user);

        $this->invitationModel->delete();

        $this->redirect(Dashboard::getUrl());
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
        ];
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label(__('filament-panels::pages/auth/register.form.actions.register.label'))
            ->submit('register');
    }

    public function getHeading(): string
    {
        return 'Accept Invitation';
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function getSubHeading(): string
    {
        return 'Create your user to accept an invitation';
    }
}
