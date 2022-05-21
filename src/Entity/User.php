<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    normalizationContext: [
        "groups" => ["user:read"]
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["customers_read", "invoices_read", "user:read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(["customers_read", "invoices_read"])]
    #[Assert\NotBlank(message:"L'email est obligatoire")]
    #[Assert\Email(message:"Cet email n'est pas valide")]
    #[Assert\Length(min:3, max:180, minMessage:"L'email doit faire au moins 3 caractères", maxMessage:"L'email doit faire au plus 180 caractères")]    
    private $email;

    #[ORM\Column(type: 'json')]
    #[Groups(["customers_read", "invoices_read", "user:read"])]

    private $roles = [];

    #[ORM\Column(type: 'string')]
    #[Groups(["customers_read", "invoices_read"])]
    #[Assert\NotBlank(message:"Le mot de passe est obligatoire")]
    #[Assert\Length(min:8, max:255, minMessage:"Le mot de passe doit faire au moins 8 caractères", maxMessage:"Le mot de passe doit faire au plus 255 caractères")]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["customers_read", "invoices_read","user:read"])]
    #[Assert\NotBlank(message:"Le prénom est obligatoire")]
    #[Assert\Length(min:3, max:255, minMessage:"Le prénom doit faire au moins 3 caractères", maxMessage:"Le prénom doit faire au plus 255 caractères")]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["customers_read", "invoices_read", "user:read"])]
    private $lastName;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Customer::class)]
    private $customers;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setUser($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getUser() === $this) {
                $customer->setUser(null);
            }
        }

        return $this;
    }
}
