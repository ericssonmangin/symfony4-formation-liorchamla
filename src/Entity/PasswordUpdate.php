<?php

namespace App\Entity;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    /**
     * @SecurityAssert\UserPassword(message="Votre password actuel n'est pas valide")
     */
    private $oldPassword;

    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Votre mot de passe doit être identique dans les deux champs requis")
     */
    private $confirmPassword;


    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
